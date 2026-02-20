<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Project extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'code',
        'description',
        'attachments',
        'location',
        'budget',
        'start_date',
        'end_date',
        'state_id',
        'lga_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'attachments' => 'array',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function procurementRequests(): HasMany
    {
        return $this->hasMany(ProcurementRequest::class);
    }

    // Helper method to check if user is assigned to project
    public function isAssignedTo(User $user, ?string $roleType = null): bool
    {
        $query = $this->assignments()->where('user_id', $user->id)->where('is_active', true);

        if ($roleType) {
            $query->where('role_type', $roleType);
        }

        return $query->exists();
    }

    // Get assigned site managers (collection)
    public function siteManagers()
    {
        return $this->belongsToMany(User::class, 'project_assignments')
            ->wherePivot('role_type', 'site_manager')
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }

    // Get assigned procurement officers (collection)
    public function procurementOfficers()
    {
        return $this->belongsToMany(User::class, 'project_assignments')
            ->wherePivot('role_type', 'procurement_officer')
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }

    // Get assigned site manager (first one)
    public function siteManager()
    {
        return $this->assignments()
            ->where('role_type', 'site_manager')
            ->where('is_active', true)
            ->first()?->user;
    }

    // Get assigned procurement officer (first one)
    public function procurementOfficer()
    {
        return $this->assignments()
            ->where('role_type', 'procurement_officer')
            ->where('is_active', true)
            ->first()?->user;
    }

    public function requests()
    {
        return $this->hasMany(ProcurementRequest::class, 'project_id');
    }

    // Get status badge class for Bootstrap
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'planning' => 'secondary',
            'active' => 'success',
            'on_hold' => 'warning',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}