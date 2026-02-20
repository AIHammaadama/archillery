<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Project;
use App\Models\Material;
use App\Services\ProcurementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcurementRequestController extends Controller
{
    protected $procurementService;

    public function __construct(ProcurementService $procurementService)
    {
        $this->procurementService = $procurementService;
    }

    /**
     * Display a listing of procurement requests
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProcurementRequest::class);

        $user = Auth::user();

        $query = ProcurementRequest::with(['project', 'requestedBy', 'procurementOfficer', 'approvedBy']);

        // Permission-based filtering
        if (!$user->hasPermission('view-all-requests')) {
            // Users without 'view-all-requests' see only their own or assigned requests
            if ($user->hasPermission('create-purchase-request')) {
                // Site Managers see only their own requests
                $query->where('requested_by', $user->id);
            } elseif ($user->hasPermission('process-purchase-request')) {
                // Procurement Officers see requests from their assigned projects
                $query->where(function ($q) use ($user) {
                    $q->where('procurement_officer_id', $user->id)
                        ->orWhereHas('project', function ($projectQuery) use ($user) {
                            $projectQuery->whereHas('assignments', function ($assignQuery) use ($user) {
                                $assignQuery->where('user_id', $user->id)
                                    ->where('role_type', 'procurement_officer')
                                    ->where('is_active', true);
                            });
                        });
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('justification', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })
                    // optional: search requester
                    ->orWhereHas('requestedBy', function ($uq) use ($search) {
                        $uq->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new procurement request
     */
    public function create(Request $request)
    {

        $user = Auth::user();

        // Get projects where user is assigned as Site Manager
        if ($user->hasPermission('create-purchase-request')) {
            // Directors can request for any active project
            $projects = Project::where('status', '!=', 'cancelled')->orderBy('name')->get();
        } else {
            // Site Managers see only their assigned projects
            $projects = Project::whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('role_type', 'site_manager')
                    ->where('is_active', true);
            })->where('status', '!=', 'cancelled')->orderBy('name')->get();
        }

        // Preselect project if passed in query string
        $selectedProjectId = $request->get('project_id');

        // Get material categories
        $categories = Material::where('is_active', true)
            ->distinct('category')
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('requests.create', compact('projects', 'categories', 'selectedProjectId'));
    }

    /**
     * Store a newly created procurement request
     */
    public function store(Request $request)
    {
        $this->authorize('create', ProcurementRequest::class);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'required_by_date' => 'nullable|date|after:today',
            'justification' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create the procurement request
            $procurementRequest = $this->procurementService->createRequest(
                $validated,
                Auth::user()
            );
            // If Director is creating request, auto-submit and auto-approve
            if (Auth::user()->hasRole('director')) {
                // Auto-submit
                $this->procurementService->submitRequest($procurementRequest, Auth::user());

                // Skip procurement officer assignment and go straight to pending director approval
                // This allows the Director to assign vendors before final approval

                $procurementRequest->update([
                    'status' => \App\Enums\RequestStatus::PENDING_DIRECTOR,
                    'procurement_officer_id' => null
                ]);

                // Add status history
                $procurementRequest->statusHistory()->create([
                    'from_status' => \App\Enums\RequestStatus::DRAFT,
                    'to_status' => \App\Enums\RequestStatus::PENDING_DIRECTOR,
                    'changed_by' => Auth::id(),
                    'comments' => 'Director Request - Pending Vendor Assignment'
                ]);
            }
            DB::commit();

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Procurement request created successfully',
                    'request_number' => $procurementRequest->request_number,
                    'redirect_url' => route('requests.show', $procurementRequest)
                ]);
            }

            return redirect()->route('requests.show', $procurementRequest)
                ->with('success', 'Procurement request created successfully. Request number: ' . $procurementRequest->request_number);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create procurement request: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Failed to create procurement request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified procurement request
     */
    public function show(ProcurementRequest $request)
    {
        $this->authorize('view', $request);

        $request->load([
            'project.state',
            'project.lga',
            'requestedBy',
            'procurementOfficer',
            'approvedBy',
            'items.material',
            'items.vendor',
            'statusHistory.changedBy',
            'deliveries.verifiedBy'
        ]);

        // Check if user can view pricing
        $canViewPricing = Auth::user()->can('viewPricing', $request);

        return view('requests.show', compact('request', 'canViewPricing'));
    }

    /**
     * Show the form for editing the specified procurement request
     */
    public function edit(ProcurementRequest $request)
    {
        $this->authorize('update', $request);

        // Only draft requests can be edited
        if (!$request->isEditable()) {
            return redirect()->route('requests.show', $request)
                ->with('error', 'Only draft requests can be edited.');
        }

        $request->load(['project', 'items.material']);

        $user = Auth::user();

        // Get projects where user is assigned as Site Manager
        $projects = Project::whereHas('assignments', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('role_type', 'site_manager')
                ->where('is_active', true);
        })->where('status', '!=', 'cancelled')->orderBy('name')->get();

        // Get material categories
        $categories = Material::where('is_active', true)
            ->distinct('category')
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('requests.edit', compact('request', 'projects', 'categories'));
    }

    /**
     * Update the specified procurement request
     */
    public function update(Request $httpRequest, ProcurementRequest $request)
    {
        $this->authorize('update', $request);

        // Only draft requests can be edited
        if (!$request->isEditable()) {
            return redirect()->route('requests.show', $request)
                ->with('error', 'Only draft requests can be edited.');
        }

        $validated = $httpRequest->validate([
            'project_id' => 'required|exists:projects,id',
            'required_by_date' => 'nullable|date|after:today',
            'justification' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update basic info
            $request->update([
                'project_id' => $validated['project_id'],
                'required_by_date' => $validated['required_by_date'] ?? null,
                'justification' => $validated['justification'],
            ]);

            // Delete existing items
            $request->items()->delete();

            // Add new items
            $totalEstimated = 0;
            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity'] * ($item['estimated_unit_price'] ?? 0);
                $totalEstimated += $itemTotal;

                $request->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'estimated_unit_price' => $item['estimated_unit_price'] ?? 0,
                    'estimated_total' => $itemTotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Update total
            $request->update([
                'total_estimated_amount' => $totalEstimated
            ]);

            DB::commit();

            return redirect()->route('requests.show', $request)
                ->with('success', 'Procurement request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update procurement request: ' . $e->getMessage());
        }
    }

    /**
     * Submit request for processing
     */
    public function submit(ProcurementRequest $request)
    {
        $this->authorize('update', $request);

        try {
            $this->procurementService->submitRequest($request, Auth::user());

            return redirect()->route('requests.show', $request)
                ->with('success', 'Request submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified procurement request
     */
    public function destroy(ProcurementRequest $request)
    {
        $this->authorize('delete', $request);

        try {
            $request->delete();
            return redirect()->route('requests.index')
                ->with('success', 'Procurement request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }
}
