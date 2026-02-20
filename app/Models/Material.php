<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Material extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * Attributes to include in the Audit.
     */
    protected $auditInclude = [
        'name',
        'code',
        'category',
        'unit_of_measurement',
        'description',
        'is_active',
    ];

    protected $fillable = [
        'name',
        'code',
        'category',
        'unit_of_measurement',
        'description',
        'specifications',
        'is_active',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function vendorMaterials(): HasMany
    {
        return $this->hasMany(VendorMaterial::class);
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_materials')
            ->withPivot('price', 'currency', 'minimum_order_quantity', 'lead_time_days', 'is_preferred', 'valid_from', 'valid_until', 'notes')
            ->withTimestamps();
    }
}
