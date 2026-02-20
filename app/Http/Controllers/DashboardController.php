<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\Role;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $dashboardData = [];

        // Get permission-based dashboard data
        if ($user->hasPermission('create-purchase-request') && !$user->hasPermission('process-purchase-request')) {
            // Site Manager dashboard
            $dashboardData = $this->dashboardService->getSiteManagerDashboard($user);
        } elseif ($user->hasPermission('process-purchase-request') && !$user->hasPermission('approve-purchase-request')) {
            // Procurement Officer dashboard
            $dashboardData = $this->dashboardService->getProcurementOfficerDashboard($user);
        } elseif ($user->hasPermission('approve-purchase-request') && !$user->hasPermission('manage-users')) {
            // Director dashboard
            $dashboardData = $this->dashboardService->getDirectorDashboard($user);
        } elseif ($user->hasPermission('manage-users')) {
            // Admin dashboard
            $dashboardData = $this->dashboardService->getAdminDashboard();
        }

        return view('dashboard.index', compact('user', 'dashboardData'));
    }

    public function profile()
    {
        $user = User::where('id', Auth::user()->id)->first();
        return view('dashboard.profile')->with('user', $user);
    }

    public function users()
    {
        $user = Auth::user();

        if (! $user->hasPermission('manage-users')) {
            abort(403, 'You do not have permission to view this resource');
        }

        // Paginate permissions FIRST
        $permissionsPaginator = Permission::orderBy('group')
            ->orderBy('name')
            ->paginate(10);

        $permissions = $permissionsPaginator->groupBy('group');

        $allPermissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get();

        $admins = User::with('role')
            ->where('id', '!=', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        $roles = Role::all();

        return view('dashboard.users', [
            'admins' => $admins,
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'permissions' => $permissions,
            'permissionsPaginator' => $permissionsPaginator,
            'user' => $user,
            'search' => '',
        ]);
    }

    public function search_users(Request $request)
    {
        $q = $request->input('q');
        $user = Auth::user();

        if (! $user->hasPermission('manage-users')) {
            abort(403, 'You do not have permission to view this resource');
        }

        $permissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $allPermissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get();

        $admins = User::with('role')
            ->where('id', '!=', auth()->id())
            ->where(function ($query) use ($q) {
                $query->where('firstname', 'like', '%' . $q . '%')
                    ->orWhere('lastname', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderBy('created_at', 'asc')
            ->get();
        $roles = Role::all();

        if (count($admins) >= 1) {
            return view('dashboard.users', [
                'admins' => $admins,
                'roles' => $roles,
                'allPermissions' => $allPermissions,
                'permissions' => $permissions,
                'user' => $user,
                'success' => 'Search result for: ' . $q,
                'search_type' => 'users',
                'search' => $q,
            ]);
        } else {
            return redirect()->route('users')->with([
                'admins' => $admins,
                'roles' => $roles,
                'permissions' => $permissions,
                'user' => $user,
                'error' => 'Nothing found for your search, try changing the keywords',
                'search' => $q,
            ]);
        }
    }

    public function audit(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('view-audits')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Audit::query()
            ->leftJoin('users', 'audits.user_id', '=', 'users.id')
            ->select('audits.*', 'users.email', 'users.firstname', 'users.lastname');

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('audits.event', $request->event);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('audits.user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('audits.auditable_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('audits.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('audits.created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('users.email', 'like', "%{$q}%")
                    ->orWhere('users.firstname', 'like', "%{$q}%")
                    ->orWhere('users.lastname', 'like', "%{$q}%")
                    ->orWhere('audits.old_values', 'like', "%{$q}%")
                    ->orWhere('audits.new_values', 'like', "%{$q}%");
            });
        }

        $audits = $query->latest('audits.created_at')->paginate(50)->withQueryString();

        // Get filter options
        $users = User::orderBy('firstname')->get();
        $modelTypes = Audit::distinct()->pluck('auditable_type')
            ->map(fn($type) => ['value' => $type, 'label' => class_basename($type)]);
        $events = ['created', 'updated', 'deleted'];

        return view('audits.index', compact('audits', 'users', 'modelTypes', 'events', 'user'));
    }

    public function search_audits(Request $request)
    {
        return $this->audit($request);
    }

    public function view_audit($id)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user->type == 0) {
            $audits = Audit::join('users', 'audits.user_id', '=', 'users.id')
                ->where('audits.id', $id)
                ->get();
            $audits->transform(fn($audit) => [
                'name'              => $audit->firstname . ' ' . $audit->lastname,
                'email'             => $audit->email,
                'event'             => $audit->event,
                'auditable_type'    => $audit->auditable_type,
                'old_values'        => $audit->old_values,
                'new_values'        => $audit->new_values,
                'url'               => $audit->url,
                'ip_address'        => $audit->ip_address,
                'user_agent'        => $audit->user_agent,
                'created_at'        => $audit->created_at
            ]);
            return view('audits.show')->with(['audits' => $audits, 'user' => $user]);
        } else {
            return redirect()->back();
        }
    }
}