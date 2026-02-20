<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'material_id',
        'price',
        'currency',
        'minimum_order_quantity',
        'lead_time_days',
        'is_preferred',
        'valid_from',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'is_preferred' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Helper to check if pricing is currently valid
    public function isValid(): bool
    {
        $now = now()->toDateString();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        return true;
    }
}
