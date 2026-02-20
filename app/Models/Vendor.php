<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Vendor extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * Attributes to include in the Audit.
     */
    protected $auditInclude = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'state_id',
        'lga_id',
        'rating',
        'status',
        'bank_name',
        'bank_account_name',
        'bank_account',
    ];

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'alt_phone',
        'address',
        'state_id',
        'lga_id',
        'business_registration',
        'tax_id',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'rating',
        'status',
        'notes',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    public function vendorMaterials(): HasMany
    {
        return $this->hasMany(VendorMaterial::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'vendor_materials')
            ->withPivot('price', 'currency', 'minimum_order_quantity', 'lead_time_days', 'is_preferred', 'valid_from', 'valid_until', 'notes')
            ->withTimestamps();
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
