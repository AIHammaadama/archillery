<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\ProcurementRequest;
use App\Models\RequestItem;
use App\Models\User;
use App\Enums\RequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Events\DeliveryReceived;

class DeliveryService
{
    /**
     * Generate a unique delivery number
     */
    public function generateDeliveryNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "DEL-{$year}{$month}";

        $lastDelivery = Delivery::where('delivery_number', 'like', "{$prefix}%")
            ->orderBy('delivery_number', 'desc')
            ->first();

        if ($lastDelivery) {
            $lastNumber = (int) substr($lastDelivery->delivery_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$newNumber}";
    }

    /**
     * Record a new delivery
     */
    public function recordDelivery(
        RequestItem $item,
        array $data,
        User $receivedBy
    ): Delivery {
        DB::beginTransaction();

        try {
            // Handle file uploads
            $attachments = [];
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $path = $file->store('deliveries', 'public');
                    $attachments[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Permission-based auto verification
            $canVerify = $receivedBy->hasPermission('verify-deliveries');

            $delivery = Delivery::create([
                'delivery_number'     => $this->generateDeliveryNumber(),
                'request_id'          => $item->request_id,
                'request_item_id'     => $item->id,
                'vendor_id'           => $item->vendor_id,
                'delivery_date'       => $data['delivery_date'],
                'quantity_delivered'  => $data['quantity_delivered'],
                'received_by'         => $receivedBy->id,
                'verification_status' => $canVerify ? 'accepted' : 'pending',
                'verified_by'         => $canVerify ? $receivedBy->id : null,
                'quality_notes'       => $data['quality_notes'] ?? null,
                'waybill_number'      => $data['waybill_number'] ?? null,
                'invoice_number'      => $data['invoice_number'] ?? null,
                'invoice_amount'      => $data['invoice_amount'] ?? null,
                'attachments'         => $attachments,
            ]);

            // Update request status if auto-verified
            if ($canVerify) {
                $this->updateRequestDeliveryStatus($delivery->request);
            }

            // Recalculate delivered quantity for this item
            $totalDelivered = $item->deliveries()
                ->whereIn('verification_status', ['accepted', 'partial'])
                ->sum('quantity_delivered');

            if ($totalDelivered <= 0) {
                $item->update(['delivery_status' => 'pending']);
            } elseif ($totalDelivered < $item->quantity) {
                $item->update(['delivery_status' => 'partial']);
            } else {
                $item->update(['delivery_status' => 'complete']);
            }

            event(new DeliveryReceived($delivery));

            DB::commit();

            return $delivery;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Verify a delivery
     */
    public function verifyDelivery(
        Delivery $delivery,
        string $status,
        User $verifiedBy,
        ?string $notes = null
    ): Delivery {
        DB::beginTransaction();

        try {
            $delivery->update([
                'verification_status' => $status,
                'verified_by' => $verifiedBy->id,
                'quality_notes' => $notes ?? $delivery->quality_notes,
            ]);

            // Check if all items are fully delivered
            $this->updateRequestDeliveryStatus($delivery->request);

            $item = $delivery->requestItem;

            $totalDelivered = $item->deliveries()
                ->whereIn('verification_status', ['accepted', 'partial'])
                ->sum('quantity_delivered');

            if ($totalDelivered >= $item->quantity) {
                $item->update(['delivery_status' => 'complete']);
            } elseif ($totalDelivered > 0) {
                $item->update(['delivery_status' => 'partial']);
            } else {
                $item->update(['delivery_status' => 'pending']);
            }

            DB::commit();

            return $delivery;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update request status based on delivery completion
     */
    protected function updateRequestDeliveryStatus(ProcurementRequest $request): void
    {
        if (
            $request->status !== RequestStatus::APPROVED &&
            $request->status !== RequestStatus::PARTIALLY_DELIVERED
        ) {
            return;
        }

        $allItemsDelivered = true;
        $anyItemDelivered = false;

        foreach ($request->items as $item) {
            $totalDelivered = $item->deliveries()
                ->whereIn('verification_status', ['accepted', 'partial'])
                ->sum('quantity_delivered');

            if ($totalDelivered >= $item->quantity) {
                $anyItemDelivered = true;
            } else {
                $allItemsDelivered = false;
            }

            if ($totalDelivered > 0) {
                $anyItemDelivered = true;
            }
        }

        if ($allItemsDelivered) {
            $request->update(['status' => RequestStatus::FULLY_DELIVERED]);
        } elseif ($anyItemDelivered) {
            $request->update(['status' => RequestStatus::PARTIALLY_DELIVERED]);
        }
    }

    /**
     * Get delivery statistics for a request
     */
    public function getRequestDeliveryStats(ProcurementRequest $request): array
    {
        $stats = [
            'total_items' => $request->items->count(),
            'items_fully_delivered' => 0,
            'items_partially_delivered' => 0,
            'items_pending' => 0,
            'overall_progress' => 0,
        ];

        $totalProgress = 0;

        foreach ($request->items as $item) {
            $totalDelivered = $item->deliveries()
                ->whereIn('verification_status', ['accepted', 'partial'])
                ->sum('quantity_delivered');

            $progress = $item->quantity > 0
                ? min(100, ($totalDelivered / $item->quantity) * 100)
                : 0;

            $totalProgress += $progress;

            if ($totalDelivered >= $item->quantity) {
                $stats['items_fully_delivered']++;
            } elseif ($totalDelivered > 0) {
                $stats['items_partially_delivered']++;
            } else {
                $stats['items_pending']++;
            }
        }

        $stats['overall_progress'] = $stats['total_items'] > 0
            ? round($totalProgress / $stats['total_items'], 2)
            : 0;

        return $stats;
    }

    /**
     * Delete delivery attachment
     */
    public function deleteAttachment(Delivery $delivery, int $index): bool
    {
        $attachments = $delivery->attachments ?? [];

        if (!isset($attachments[$index])) {
            return false;
        }

        // Delete file from storage
        Storage::disk('public')->delete($attachments[$index]['path']);

        // Remove from array
        array_splice($attachments, $index, 1);

        // Update delivery
        $delivery->update(['attachments' => $attachments]);

        return true;
    }
}