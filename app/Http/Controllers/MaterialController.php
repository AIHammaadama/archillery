<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials
     */
    public function index(Request $request)
    {
        $query = Material::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $materials = $query
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(21)
            ->withQueryString(); // 👈 keeps filters during pagination

        $categories = Material::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('materials.index', compact('materials', 'categories'));
    }

    /**
     * Show the form for creating a new material
     */
    public function create()
    {
        // Only admins can create materials
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $categories = config('materials.categories');
        $units = config('materials.units_of_measurement');

        return view('materials.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created material
     */
    public function store(Request $request)
    {
        // Only admins can create materials
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit_of_measurement' => 'required|string|max:50',
            'description' => 'nullable|string',
            'specifications' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        try {
            // Auto-generate material code
            $code = $this->generateMaterialCode();

            // Decode specifications JSON if present
            $specifications = null;
            if (!empty($validated['specifications'])) {
                $specifications = json_decode($validated['specifications'], true);
            }

            $material = Material::create([
                'name' => $validated['name'],
                'code' => $code,
                'category' => $validated['category'],
                'unit_of_measurement' => $validated['unit_of_measurement'],
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('materials.index')
                ->with('success', "Material created successfully with code: {$code}");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create material: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique material code
     */
    private function generateMaterialCode(): string
    {
        $year = date('Y');
        $padding = config('materials.code_sequence_padding', 4);

        // Find the latest material code for this year
        $latestMaterial = Material::where('code', 'like', "MAT-{$year}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($latestMaterial) {
            // Extract sequence number from the latest code
            $parts = explode('-', $latestMaterial->code);
            $lastSequence = isset($parts[2]) ? (int) $parts[2] : 0;
            $newSequence = $lastSequence + 1;
        } else {
            // First material of the year
            $newSequence = 1;
        }

        $sequence = str_pad($newSequence, $padding, '0', STR_PAD_LEFT);

        return "MAT-{$year}-{$sequence}";
    }

    /**
     * Display the specified material
     */
    public function show(Material $material)
    {
        $material->load(['vendors' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('vendor_materials.price');
        }]);

        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified material
     */
    public function edit(Material $material)
    {
        // Only admins can edit materials
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $categories = config('materials.categories');
        $units = config('materials.units_of_measurement');

        return view('materials.edit', compact('material', 'categories', 'units'));
    }

    /**
     * Update the specified material
     */
    public function update(Request $request, Material $material)
    {
        // Only admins can update materials
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit_of_measurement' => 'required|string|max:50',
            'description' => 'nullable|string',
            'specifications' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        try {
            // Decode specifications JSON if present
            $specifications = null;
            if (!empty($validated['specifications'])) {
                $specifications = json_decode($validated['specifications'], true);
            }

            $material->update([
                'name' => $validated['name'],
                // Don't update code - it's auto-generated and should remain unchanged
                'category' => $validated['category'],
                'unit_of_measurement' => $validated['unit_of_measurement'],
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('materials.show', $material)
                ->with('success', 'Material updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update material: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified material
     */
    public function destroy(Material $material)
    {
        // Only admins can delete materials
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $material->delete();
            return redirect()->route('materials.index')
                ->with('success', 'Material deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete material: ' . $e->getMessage());
        }
    }

    /**
     * API: Search materials for cart/request creation
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $category = $request->get('category', null);

        $query = Material::where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $materials = $query->orderBy('name')->limit(50)->get();

        return response()->json($materials);
    }
}