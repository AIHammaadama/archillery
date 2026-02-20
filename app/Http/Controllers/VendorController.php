<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        // Allow access for users with view-vendors permission or admin roles
        if (!Auth::user()->hasPermission('view-vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Vendor::with('state');

        // Fetch all vendors (DataTables handles filtering and pagination)
        $vendors = $query->orderBy('name')->get();

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('create-vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $states = State::where('status', 1)->orderBy('state')->get();

        return view('vendors.create', compact('states'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create-vendors')) {
            abort(403, 'Unauthorized action.');
        }

        #return $request->all();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'alt_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'lga_id' => 'nullable|exists:lgas,id',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:24',
            'rating' => 'nullable|numeric|min:1|max:5',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        try {
            // Map form fields to database columns
            $vendorData = [
                'name' => $validated['name'],
                'code' => $this->generateVendorCode(),
                'contact_person' => $validated['contact_person'],
                'alt_phone' => $validated['alt_phone'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'state_id' => $validated['state_id'],
                'lga_id' => $validated['lga_id'] ?? null,
                'business_registration' => $validated['registration_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_account' => $validated['account_number'] ?? null,
                'bank_account_name' => $validated['bank_account_name'] ?? null,
                'tax_id' => $validated['tax_id'] ?? null,
                'rating' => $validated['rating'] ?? 0,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ];

            Vendor::create($vendorData);

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique vendor code
     */
    private function generateVendorCode(): string
    {
        $year = date('Y');
        $count = Vendor::whereYear('created_at', $year)->count() + 1;
        return sprintf('VEN-%s-%04d', $year, $count);
    }

    public function show(Vendor $vendor)
    {
        // Allow access for users with view-vendors permission or admin roles
        if (!Auth::user()->can('view-vendors') && !Auth::user()->hasAnyRole(['admin', 'super_admin', 'director', 'procurement_officer'])) {
            abort(403, 'Unauthorized action.');
        }

        $vendor->load(['state', 'materials' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin', 'procurement_officer'])) {
            abort(403, 'Unauthorized action.');
        }

        $states = State::where('status', 1)->orderBy('state')->get();
        $lgas = [];

        if ($vendor->state_id) {
            $lgas = \App\Models\Lga::where('state_id', $vendor->state_id)
                ->orderBy('lga')
                ->get();
        }

        return view('vendors.edit', compact('vendor', 'states', 'lgas'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin', 'procurement_officer'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'alt_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'lga_id' => 'nullable|exists:lgas,id',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:24',
            'rating' => 'nullable|numeric|min:1|max:5',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        try {
            // Map form fields to database columns
            $vendorData = [
                'name' => $validated['name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'alt_phone' => $validated['alt_phone'] ?? null,
                'address' => $validated['address'],
                'state_id' => $validated['state_id'],
                'lga_id' => $validated['lga_id'] ?? null,
                'business_registration' => $validated['registration_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_account' => $validated['account_number'] ?? null,
                'bank_account_name' => $validated['bank_account_name'] ?? null,
                'tax_id' => $validated['tax_id'] ?? null,
                'rating' => $validated['rating'] ?? $vendor->rating,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ];

            $vendor->update($vendorData);

            return redirect()->route('vendors.show', $vendor)
                ->with('success', 'Vendor updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    public function destroy(Vendor $vendor)
    {
        if (!Auth::user()->hasAnyRole(['director', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $vendor->delete();
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign materials to vendor
     */
    public function assignMaterials(Vendor $vendor)
    {
        if (!Auth::user()->can('manage-vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $vendor->load('materials');
        $materials = \App\Models\Material::where('is_active', true)->orderBy('name')->get();

        return view('vendors.assign-materials', compact('vendor', 'materials'));
    }

    /**
     * Store material assignments for vendor
     */
    public function storeMaterialAssignments(Request $request, Vendor $vendor)
    {
        if (!Auth::user()->can('manage-vendors')) {
            abort(403, 'Unauthorized action.');
        }


        $validated = $request->validate([
            'materials' => 'nullable|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.price' => 'required|numeric|min:0',
            'materials.*.minimum_order_quantity' => 'nullable|numeric|min:0',
            'materials.*.lead_time_days' => 'nullable|integer|min:0',
            'materials.*.valid_until' => 'nullable|date|after:today',
        ]);

        try {
            // Sync materials with pivot data
            $syncData = [];
            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $materialData) {
                    $syncData[$materialData['material_id']] = [
                        'price' => $materialData['price'],
                        'minimum_order_quantity' => $materialData['minimum_order_quantity'] ?? 1,
                        'lead_time_days' => $materialData['lead_time_days'] ?? null,
                        'valid_until' => $materialData['valid_until'] ?? null,
                    ];
                }
            }

            $vendor->materials()->sync($syncData);

            return redirect()->route('vendors.show', $vendor)
                ->with('success', 'Material assignments updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update material assignments: ' . $e->getMessage());
        }
    }

    /**
     * API: Get material price for a specific vendor
     */
    public function getMaterialPrice(Vendor $vendor, $materialId)
    {
        // Check permission
        if (!Auth::user()->hasPermission('view-request-pricing')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $vendorMaterial = \App\Models\VendorMaterial::where('vendor_id', $vendor->id)
            ->where('material_id', $materialId)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (!$vendorMaterial) {
            return response()->json([
                'price' => null,
                'currency' => 'NGN',
                'is_valid' => false,
                'message' => 'No pricing found for this vendor-material combination'
            ]);
        }

        return response()->json([
            'price' => $vendorMaterial->price,
            'currency' => $vendorMaterial->currency ?? 'NGN',
            'is_valid' => $vendorMaterial->isValid(),
            'minimum_order_quantity' => $vendorMaterial->minimum_order_quantity,
            'lead_time_days' => $vendorMaterial->lead_time_days,
        ]);
    }
}
