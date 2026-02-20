<?php

namespace App\Services;

use App\Models\ProcurementRequest;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\Project;
use App\Enums\RequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementService
{
    /**
     * Create a new procurement request
     */
    public function createRequest(array $data, User $user): ProcurementRequest
    {
        DB::beginTransaction();
        try {
            // Generate request number
            $requestNumber = $this->generateRequestNumber();

            // Create the request
            $request = ProcurementRequest::create([
                'request_number' => $requestNumber,
                'project_id' => $data['project_id'],
                'requested_by' => $user->id,
                'request_date' => $data['request_date'] ?? now()->toDateString(),
                'required_by_date' => $data['required_by_date'] ?? null,
                'status' => RequestStatus::DRAFT,
                'justification' => $data['justification'] ?? null,
            ]);

            // Add items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addItemToRequest($request, $item);
                }
            }

            DB::commit();
            return $request->fresh('items.material');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create procurement request: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add item to a request
     */
    public function addItemToRequest(ProcurementRequest $request, array $itemData): RequestItem
    {
        $item = $request->items()->create([
            'material_id' => $itemData['material_id'],
            'quantity' => $itemData['quantity'],
            'unit_of_measurement' => $itemData['unit_of_measurement'] ?? null,
            'estimated_unit_price' => $itemData['estimated_unit_price'] ?? null,
            'specifications' => $itemData['specifications'] ?? null,
            'remarks' => $itemData['remarks'] ?? null,
        ]);

        // Recalculate request totals
        $this->recalculateRequestTotals($request);

        return $item;
    }

    /**
     * Submit request for procurement
     */
    public function submitRequest(ProcurementRequest $request, User $user): bool
    {
        if (!$request->canBeSubmitted()) {
            throw new \Exception('Request cannot be submitted. Ensure it has items.');
        }

        $request->changeStatusTo(RequestStatus::SUBMITTED, $user, 'Request submitted for procurement');

        // Assign to procurement officer from project
        $procurementOfficer = $request->project->procurementOfficer();
        if ($procurementOfficer) {
            $request->update([
                'procurement_officer_id' => $procurementOfficer->id,
                'assigned_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Procurement officer accepts request
     */
    public function acceptRequest(ProcurementRequest $request, User $user): bool
    {
        $request->changeStatusTo(RequestStatus::PENDING_PROCUREMENT, $user, 'Request accepted by procurement officer');
        return true;
    }

    /**
     * Procurement officer starts processing
     */
    public function startProcessing(ProcurementRequest $request, User $user): bool
    {
        $request->changeStatusTo(RequestStatus::PROCUREMENT_PROCESSING, $user, 'Started vendor assignment');
        return true;
    }

    /**
     * Assign vendor to request item
     */
    public function assignVendorToItem(RequestItem $item, int $vendorId, float $quotedPrice): RequestItem
    {
        $item->update([
            'vendor_id' => $vendorId,
            'quoted_unit_price' => $quotedPrice,
        ]);

        // Recalculate request totals
        $this->recalculateRequestTotals($item->request);

        return $item->fresh();
    }

    /**
     * Submit request to director for approval
     */
    public function submitForApproval(ProcurementRequest $request, User $user): bool
    {
        // Ensure all items have vendors assigned
        $itemsWithoutVendors = $request->items()->whereNull('vendor_id')->count();
        if ($itemsWithoutVendors > 0) {
            throw new \Exception("Cannot submit for approval. {$itemsWithoutVendors} items do not have vendors assigned.");
        }
        
        $request->changeStatusTo(RequestStatus::PENDING_DIRECTOR, $user, 'Submitted for director approval');
        return true;
    }

    /**
     * Director approves request
     */
    public function approveRequest(ProcurementRequest $request, User $user, ?string $comments = null): bool
    {
        DB::beginTransaction();
        try {
            $request->update([
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $request->changeStatusTo(RequestStatus::APPROVED, $user, $comments ?? 'Request approved');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Director rejects request
     */
    public function rejectRequest(ProcurementRequest $request, User $user, string $reason): bool
    {
        $request->update([
            'rejection_reason' => $reason,
        ]);

        $request->changeStatusTo(RequestStatus::REJECTED, $user, $reason);
        return true;
    }

    /**
     * Send request back for revision
     */
    public function sendBackForRevision(ProcurementRequest $request, User $user, string $reason): bool
    {
        $targetStatus = match($request->status) {
            RequestStatus::PENDING_DIRECTOR => RequestStatus::PROCUREMENT_PROCESSING,
            RequestStatus::PROCUREMENT_PROCESSING => RequestStatus::PENDING_PROCUREMENT,
            RequestStatus::PENDING_PROCUREMENT => RequestStatus::SUBMITTED,
            default => throw new \Exception('Cannot send back from current status'),
        };

        $request->changeStatusTo($targetStatus, $user, "Sent back for revision: {$reason}");
        return true;
    }

    /**
     * Update delivery status based on items
     */
    public function updateDeliveryStatus(ProcurementRequest $request): void
    {
        $totalItems = $request->items()->count();
        $fullyDeliveredItems = $request->items()->where('delivery_status', 'complete')->count();
        $partiallyDeliveredItems = $request->items()->whereIn('delivery_status', ['partial', 'complete'])->count();

        if ($fullyDeliveredItems === $totalItems && $totalItems > 0) {
            // All items fully delivered
            if ($request->status !== RequestStatus::FULLY_DELIVERED) {
                $request->update(['status' => RequestStatus::FULLY_DELIVERED]);
            }
        } elseif ($partiallyDeliveredItems > 0) {
            // Some items delivered
            if ($request->status !== RequestStatus::PARTIALLY_DELIVERED) {
                $request->update(['status' => RequestStatus::PARTIALLY_DELIVERED]);
            }
        }
    }

    /**
     * Recalculate request totals
     */
    private function recalculateRequestTotals(ProcurementRequest $request): void
    {
        $totalEstimated = $request->items()->sum(DB::raw('quantity * COALESCE(estimated_unit_price, 0)'));
        $totalQuoted = $request->items()->sum(DB::raw('quantity * COALESCE(quoted_unit_price, 0)'));

        $request->update([
            'total_estimated_amount' => $totalEstimated,
            'total_quoted_amount' => $totalQuoted,
        ]);
    }

    /**
     * Generate unique request number
     */
    private function generateRequestNumber(): string
    {
        $year = date('Y');
        $month = date('m');

        // Count requests this month
        $count = ProcurementRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('REQ-%s-%s-%04d', $year, $month, $count);
    }

    /**
     * Get pending requests for procurement officer
     */
    public function getPendingRequestsForOfficer(User $user)
    {
        return ProcurementRequest::where('procurement_officer_id', $user->id)
            ->whereIn('status', [RequestStatus::PENDING_PROCUREMENT->value, RequestStatus::PROCUREMENT_PROCESSING->value])
            ->with(['project', 'requestedBy', 'items.material'])
            ->latest()
            ->get();
    }

    /**
     * Get pending approvals for director
     */
    public function getPendingApprovalsForDirector()
    {
        return ProcurementRequest::where('status', RequestStatus::PENDING_DIRECTOR->value)
            ->with(['project', 'requestedBy', 'procurementOfficer', 'items.material', 'items.vendor'])
            ->latest()
            ->get();
    }
}