<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class RequestItem extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * Attributes to include in the Audit.
     */
    protected $auditInclude = [
        'quantity',
        'vendor_id',
        'quoted_unit_price',
        'quantity_delivered',
        'delivery_status',
        'remarks',
    ];

    protected $fillable = [
        'request_id',
        'material_id',
        'quantity',
        'unit_of_measurement',
        'estimated_unit_price',
        'quoted_unit_price',
        'vendor_id',
        'quantity_delivered',
        'delivery_status',
        'specifications',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_unit_price' => 'decimal:2',
        'quoted_unit_price' => 'decimal:2',
        'quantity_delivered' => 'decimal:2',
        'request_id' => 'integer',
    ];

    // Relationships
    public function request(): BelongsTo
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    // Alias for request relationship (used in some views)
    public function procurementRequest(): BelongsTo
    {
        return $this->request();
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'request_item_id');
    }

    // Helper methods
    public function getTotalEstimatedAttribute(): float
    {
        return $this->quantity * ($this->estimated_unit_price ?? 0);
    }

    public function getTotalQuotedAttribute(): float
    {
        return $this->quantity * ($this->quoted_unit_price ?? 0);
    }

    // Alias for backward compatibility
    public function getQuotedTotalAttribute(): float
    {
        return $this->total_quoted;
    }

    // Database column accessor
    public function getQuotedTotalPriceAttribute(): ?float
    {
        return $this->quoted_unit_price ? $this->quantity * $this->quoted_unit_price : null;
    }

    public function getRemainingQuantityAttribute(): float
    {
        return $this->quantity - $this->quantity_delivered;
    }

    public function isFullyDelivered(): bool
    {
        return $this->delivery_status === 'complete';
    }
}