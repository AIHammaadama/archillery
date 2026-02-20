<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Delivery extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'delivery_number',
        'request_id',
        'request_item_id',
        'vendor_id',
        'delivery_date',
        'quantity_delivered',
        'received_by',
        'verified_by',
        'verification_status',
        'quality_notes',
        'waybill_number',
        'invoice_number',
        'invoice_amount',
        'attachments',
        'site_manager_status',
        'site_manager_comments',
        'site_manager_updated_by',
        'site_manager_updated_at',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'quantity_delivered' => 'decimal:2',
        'invoice_amount' => 'decimal:2',
        'attachments' => 'array',
        'site_manager_updated_at' => 'datetime',
    ];

    // Relationships
    public function request(): BelongsTo
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(RequestItem::class, 'request_item_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function siteManagerUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'site_manager_updated_by');
    }

    // Helper methods
    public function isVerified(): bool
    {
        return in_array($this->verification_status, ['accepted', 'partial']);
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function siteManagerCanUpdate(): bool
    {
        // Site manager can update if delivery is verified
        return in_array($this->verification_status, ['accepted', 'partial']);
    }

    public function siteManagerHasUpdated(): bool
    {
        return !is_null($this->site_manager_updated_at);
    }
}
