<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Vendor;
use App\Services\ProcurementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    protected $procurementService;

    public function __construct(ProcurementService $procurementService)
    {
        $this->procurementService = $procurementService;
    }

    /**
     * Procurement Officer: View pending requests for vendor assignment
     */
    public function procurementQueue()
    {
        $user = Auth::user();

        // Check permission instead of hard-coded role
        if (!$user->hasPermission('process-purchase-request')) {
            abort(403, 'You do not have permission to access procurement queue.');
        }

        $requests = ProcurementRequest::with(['project', 'requestedBy'])
            ->whereIn('status', ['submitted', 'pending_procurement', 'procurement_processing'])
            ->whereHas('project', function ($query) use ($user) {
                $query->whereHas('assignments', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('role_type', 'procurement_officer')
                        ->where('is_active', true);
                });
            })
            ->latest()
            ->paginate(15);

        return view('approvals.procurement-queue', compact('requests'));
    }

    /**
     * Procurement Officer: Vendor assignment form
     */
    public function assignVendors(ProcurementRequest $request)
    {
        $user = Auth::user();

        // Check permission instead of hard-coded role
        if (!$user->hasPermission('assign-vendors')) {
            abort(403, 'You do not have permission to access vendor assignment.');
        }

        $request->load(['project', 'items.material.vendors', 'items.vendor']);

        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('approvals.assign-vendors', compact('request', 'vendors'));
    }

    /**
     * Procurement Officer: Save vendor assignments
     */
    public function saveVendorAssignments(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        // Check permission instead of hard-coded role
        if (!$user->hasPermission('assign-vendors')) {
            abort(403, 'You do not have permission to access resource.');
        }

        $validated = $httpRequest->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:request_items,id',
            'items.*.vendor_id' => 'required|exists:vendors,id',
            'items.*.quoted_unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Ensure request is in the correct status for vendor assignment
            // Transition through proper states if needed
            if ($request->status->value === 'submitted') {
                $this->procurementService->acceptRequest($request, $user);
                $request->refresh();
            }

            if ($request->status->value === 'pending_procurement') {
                $this->procurementService->startProcessing($request, $user);
                $request->refresh();
            }

            // Assign vendors to items
            foreach ($validated['items'] as $itemData) {
                $item = $request->items()->find($itemData['item_id']);
                if ($item) {
                    $this->procurementService->assignVendorToItem(
                        $item,
                        $itemData['vendor_id'],
                        $itemData['quoted_unit_price']
                    );
                }
            }

            // Reload the request to get updated relationships and totals
            $request->refresh();

            // Submit for director approval
            $this->procurementService->submitForApproval($request, $user);

            DB::commit();

            return redirect()->route('approvals.procurement-queue')
                ->with('success', 'Vendor assignments saved and submitted for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save assignments: ' . $e->getMessage());
        }
    }

    /**
     * Director: View approval queue
     */
    public function directorQueue()
    {
        $user = Auth::user();

        // Check permission instead of hard-coded role
        if (!$user->hasPermission('approve-purchase-request')) {
            abort(403, 'You do not have permission to access approval queue.');
        }

        $requests = ProcurementRequest::with(['project', 'requestedBy', 'procurementOfficer'])
            ->where('status', 'pending_director')
            ->latest()
            ->paginate(15);

        return view('approvals.director-queue', compact('requests'));
    }

    /**
     * Director: Approve request
     */
    public function approve(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        if (!$user->can('approve', $request)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $httpRequest->validate([
            'comments' => 'nullable|string'
        ]);

        try {
            $this->procurementService->approveRequest(
                $request,
                $user,
                $validated['comments'] ?? null
            );

            return redirect()->route('approvals.director-queue')
                ->with('success', 'Request approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    /**
     * Director: Reject request
     */
    public function reject(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        if (!$user->can('reject', $request)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $httpRequest->validate([
            'reason' => 'required|string'
        ]);

        try {
            $this->procurementService->rejectRequest(
                $request,
                $user,
                $validated['reason']
            );

            return redirect()->route('approvals.director-queue')
                ->with('success', 'Request rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }

    /**
     * Director: Send back for revision
     */
    public function sendBack(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        // Use policy check
        $this->authorize('reject', $request);

        $validated = $httpRequest->validate([
            'reason' => 'required|string'
        ]);

        try {
            $this->procurementService->sendBackForRevision(
                $request,
                $user,
                $validated['reason']
            );

            return redirect()->route('approvals.director-queue')
                ->with('success', 'Request sent back for revision.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send back: ' . $e->getMessage());
        }
    }

    /**
     * Director: Show form to edit vendor assignments and prices
     */
    public function editAssignment(ProcurementRequest $request)
    {
        $user = Auth::user();

        // Check permission to edit vendor assignments
        if (!$user->hasPermission('assign-vendors') && !$user->hasRole('director')) {
            abort(403, 'You do not have permission to edit vendor assignments.');
        }

        // Request must be pending director approval
        if ($request->status->value !== 'pending_director') {
            return redirect()->route('requests.show', $request)
                ->with('error', 'Vendor assignments can only be edited when request is pending director approval.');
        }

        $request->load(['project', 'requestedBy', 'items.material', 'items.vendor']);

        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('approvals.edit-assignment', compact('request', 'vendors'));
    }

    public function updateAssignment(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        // Check permission to edit vendor assignments
        if (!$user->hasPermission('assign-vendors') && !$user->hasRole('director')) {
            abort(403, 'You do not have permission to edit vendor assignments.');
        }

        // Request must be pending director approval
        if ($request->status->value !== 'pending_director') {
            return redirect()->route('requests.show', $request)
                ->with('error', 'Vendor assignments can only be edited when request is pending director approval.');
        }

        $validated = $httpRequest->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:request_items,id',
            'items.*.vendor_id' => 'required|exists:vendors,id',
            'items.*.quoted_unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update vendor assignments for each item
            foreach ($validated['items'] as $itemData) {
                $item = $request->items()->find($itemData['item_id']);
                if ($item) {
                    $this->procurementService->assignVendorToItem(
                        $item,
                        $itemData['vendor_id'],
                        $itemData['quoted_unit_price']
                    );
                }
            }

            // Reload the request to get updated relationships and totals
            $request->refresh();

            // If Director is updating assignment, auto-approve
            if ($user->hasRole('director')) {
                $this->procurementService->approveRequest($request, $user, 'Auto-approved after vendor assignment');
                DB::commit();
                return redirect()->route('requests.show', $request)
                    ->with('success', 'Vendor assignments updated and request auto-approved successfully.');
            }

            DB::commit();

            return redirect()->route('requests.show', $request)
                ->with('success', 'Vendor assignments updated successfully. You can now approve or send back for revision.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update assignments: ' . $e->getMessage());
        }
    }
}