<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use App\Enums\RequestStatus;
use App\Events\RequestStatusChanged;

class ProcurementRequest extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'request_number',
        'project_id',
        'requested_by',
        'request_date',
        'required_by_date',
        'status',
        'procurement_officer_id',
        'assigned_at',
        'approved_by',
        'approved_at',
        'total_estimated_amount',
        'total_quoted_amount',
        'justification',
        'remarks',
        'rejection_reason',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_by_date' => 'date',
        'assigned_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_estimated_amount' => 'decimal:2',
        'total_quoted_amount' => 'decimal:2',
    ];

    // Accessor
    public function getStatusAttribute($value): ?RequestStatus
    {
        if (is_null($value)) {
            return null; // optional: or RequestStatus::DRAFT
        }

        return RequestStatus::tryFrom($value)
            ?? throw new \LogicException("Invalid RequestStatus: {$value}");
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function procurementOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurement_officer_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RequestItem::class, 'request_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(RequestStatusHistory::class, 'request_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'request_id');
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class, 'procurement_request_id');
    }

    // Helper methods
    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === RequestStatus::DRAFT && $this->items()->count() > 0;
    }

    /**
     * Transition request to a new status with validation
     */
    public function changeStatusTo(RequestStatus $newStatus, User $user, ?string $comment = null): void
    {
        $oldStatus = $this->status;

        if (!$oldStatus) {
            throw new \LogicException(
                "Request status is invalid or cannot be cast to RequestStatus enum. DB value: {$this->getOriginal('status')}"
            );
        }

        if (!$oldStatus->canTransitionTo($newStatus, $user)) {
            throw new \Exception(
                "Cannot transition from {$oldStatus->value} to {$newStatus->value}"
            );
        }

        // Update the status
        $this->update(['status' => $newStatus->value]); // <-- save enum string, not object

        // Record in history
        RequestStatusHistory::create([
            'request_id' => $this->id,
            'from_status' => $oldStatus->value,
            'to_status' => $newStatus->value,
            'changed_by' => $user->id,
            'comments' => $comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch event
        event(new RequestStatusChanged($this, $oldStatus, $newStatus, $user, $comment));
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status->badgeClass();
    }
}