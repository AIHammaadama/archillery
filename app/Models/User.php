<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\UUID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, UUID;
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'othername',
        'email',
        'phone',
        'password',
        'photo',
        'status',
        'role_id',
    ];

    protected $auditInclude = [
        'firstname',
        'lastname',
        'othername',
        'email',
        'phone',
        'photo',
        'status',
    ];

    protected $auditExclude = [
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profileCompletionPercentage()
    {
        $requiredFields = ['firstname', 'lastname', 'gender', 'birthday', 'nin', 'nin_slip', 'email', 'phone', 'photo', 'state_id', 'lga_id', 'r_state_id', 'r_lga_id', 'address', 'landmark', 'interest_id'];
        $completedFields = array_filter($requiredFields, function ($field) {
            return !empty($this->{$field});
        });
        return (count($completedFields) / count($requiredFields)) * 100;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->permissions->contains('slug', $permission);
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->role && in_array($this->role->slug, $roleSlugs);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if (!$this->role) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    // Procurement relationships
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function projectAssignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function assignedProjects()
    {
        return $this->hasManyThrough(
            Project::class,
            ProjectAssignment::class,
            'user_id',
            'id',
            'id',
            'project_id'
        )->where('project_assignments.is_active', true);
    }

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'requested_by');
    }

    public function assignedProcurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'procurement_officer_id');
    }

    public function approvedRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'approved_by');
    }

    public function deliveriesReceived()
    {
        return $this->hasMany(Delivery::class, 'received_by');
    }

    public function deliveriesVerified()
    {
        return $this->hasMany(Delivery::class, 'verified_by');
    }
}