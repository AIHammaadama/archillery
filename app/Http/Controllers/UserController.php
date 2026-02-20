<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /* ---------------- User Management ---------------- */

    public function new_user(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        $new_user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'role_id' => $request->role_id
        ]);

        if ($new_user) {
            return redirect()->route('users')->with(['success' => 'User created successfully']);
        }

        return redirect()->back()->with(['error' => 'Failed to create user.']);
    }

    public function update_user(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'phone'     => ['required', 'string', 'max:14', 'regex:/^[\+]?[0-9]{10,14}$/'],
            'role_id'   => ['required', 'exists:roles,id'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'], // optional password
        ]);

        // Prepare data to update
        $data = [
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'status'    => $request->status,
            'role_id'   => $request->role_id,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $update_user = User::where('id', $request->id)->update($data);

        if ($update_user) {
            return redirect()->route('users')->with(['success' => 'User updated successfully']);
        }

        return redirect()->back()->with(['error' => 'Failed to update user.']);
    }

    public function delete_user(Request $request)
    {
        $deleted = User::where('id', $request->id)->delete();

        return $deleted
            ? redirect()->route('users')->with('success', 'Administrator deleted successfully')
            : redirect()->back()->with('error', 'Failed to delete administrator, try again.');
    }

    /* ---------------- Roles & Permissions ---------------- */

    public function index()
    {
        $user = Auth::user();
        $adminsCount = User::where('status', 1)->count();

        return view('dashboard.index')->with([
            'user' => $user,
            'admins' => $adminsCount,
        ]);
    }

    public function create_permission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'slug' => 'required|string|unique:permissions,slug',
            'group' => 'nullable|string',
        ]);

        Permission::create($request->only('name', 'slug', 'group'));

        return back()->with('success', 'Permission created successfully.');
    }

    public function edit_permission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:permissions,id',
            'name' => 'required|string|unique:permissions,name,' . $request->id,
            'slug' => 'required|string|unique:permissions,slug,' . $request->id,
            'group' => 'nullable|string',
        ]);

        $updated = Permission::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'group' => $request->group,
        ]);

        return $updated
            ? redirect()->route('users')->with('success', 'Permission updated successfully')
            : redirect()->back()->with('error', 'Failed to update permission, try again.');
    }

    public function delete_permission(Request $request)
    {
        $deleted = Permission::where('id', $request->id)->delete();

        return $deleted
            ? redirect()->route('users')->with('success', 'Permission deleted successfully')
            : redirect()->back()->with('error', 'Failed to delete permission, try again.');
    }

    public function search_permissions(Request $request)
    {
        $q = $request->input('q');
        $user = Auth::user();

        if (! $user->hasPermission('manage-permissions')) {
            abort(403, 'You do not have permission to view this resource');
        }

        // Base query
        $query = Permission::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('group', 'like', "%{$q}%");
            });
        }

        // PAGINATE THE SAME QUERY
        $permissionsPaginator = $query
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(10);

        // Group AFTER pagination
        $permissions = $permissionsPaginator->getCollection()->groupBy('group');

        $allPermissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get();

        $admins = User::with('role')
            ->where('id', '!=', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        $roles = Role::all();

        if ($permissionsPaginator->total() > 0) {
            return view('dashboard.users', [
                'admins' => $admins,
                'roles' => $roles,
                'allPermissions' => $allPermissions,
                'permissions' => $permissions,
                'permissionsPaginator' => $permissionsPaginator,
                'user' => $user,
                'success' => 'Search result for: ' . $q,
                'search_type' => 'permissions',
                'search' => $q,
            ]);
        }

        return redirect()->route('users')->with([
            'error' => 'No permissions found for: ' . $q,
        ]);
    }

    public function create_role(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'slug' => 'required|string|unique:permissions,slug',
        ]);

        $role = Role::create($request->only('name', 'slug'));

        // Optionally attach permissions immediately
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return back()->with('success', 'Role created successfully.');
    }

    public function edit_role(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,id',
            'name' => 'required|string|unique:roles,name,' . $request->id,
            'slug' => 'required|string|unique:roles,slug,' . $request->id,
        ]);

        $updated = Role::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return $updated
            ? redirect()->route('users')->with('success', 'Role updated successfully')
            : redirect()->back()->with('error', 'Failed to update role, try again.');
    }

    public function delete_role(Request $request)
    {
        $deleted = Role::where('id', $request->id)->delete();

        return $deleted
            ? redirect()->route('users')->with('success', 'Role deleted successfully')
            : redirect()->back()->with('error', 'Failed to delete role, try again.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return back()->with('success', 'Permissions updated successfully.');
    }

    /* ---------------- Profile & Location ---------------- */

    public function update_profile(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:14', 'regex:/^[\+]?[0-9]{10,14}$/'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
            'photo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = Auth::user();

        $data = [
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'phone'     => $request->phone,
        ];

        /**
         * 🔐 Update password only if provided
         */
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        /**
         * 📸 Handle profile photo upload
         */
        if ($request->hasFile('photo')) {

            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
                Storage::disk('public')->delete('profile/' . $user->photo);
            }

            $file = $request->file('photo');

            // Generate safe unique filename
            $filename = 'user_' . $user->id . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store file
            $file->storeAs('profile', $filename, 'public');

            // Save filename in DB
            $data['photo'] = $filename;
        }

        $user->update($data);

        return redirect()
            ->route('profile')
            ->with('success', 'Profile updated successfully');
    }

    public function states()
    {
        return ['states' => State::get(['state', 'id'])];
    }

    public function lgas($state_id)
    {
        $lgas = Lga::where('state_id', $state_id)->orderBy('lga', 'ASC')->get();

        if ($lgas->isNotEmpty()) {
            $options = '<option value="" hidden>Select LGA</option>';
            foreach ($lgas as $lga) {
                $options .= "<option value='{$lga->id}'>{$lga->lga}</option>";
            }
            return $options;
        }

        return '';
    }
}