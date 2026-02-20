<?php

namespace App\Enums;

use App\Models\User;

enum RequestStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case PENDING_PROCUREMENT = 'pending_procurement';
    case PROCUREMENT_PROCESSING = 'procurement_processing';
    case PENDING_DIRECTOR = 'pending_director';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case PARTIALLY_DELIVERED = 'partially_delivered';
    case FULLY_DELIVERED = 'fully_delivered';

    /**
     * Get the human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::PENDING_PROCUREMENT => 'Pending Procurement',
            self::PROCUREMENT_PROCESSING => 'Procurement Processing',
            self::PENDING_DIRECTOR => 'Pending Director Approval',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::PARTIALLY_DELIVERED => 'Partially Delivered',
            self::FULLY_DELIVERED => 'Fully Delivered',
        };
    }

    /**
     * Get the badge color class for UI display
     */


    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::SUBMITTED => 'info',
            self::PENDING_PROCUREMENT => 'warning',
            self::PROCUREMENT_PROCESSING => 'primary',
            self::PENDING_DIRECTOR => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'dark',
            self::PARTIALLY_DELIVERED => 'info',
            self::FULLY_DELIVERED => 'success',
        };
    }

    /**
     * Check if transition to new status is allowed for the given user
     */
    public function canTransitionTo(RequestStatus $newStatus, User $user): bool
    {
        return match ($this) {
            self::DRAFT => match ($newStatus) {
                self::SUBMITTED => $user->hasAnyRole(['site_manager', 'procurement_officer', 'director', 'admin', 'super_admin']),
                self::CANCELLED => $user->hasAnyRole(['site_manager', 'procurement_officer', 'director', 'admin', 'super_admin']),
                default => false,
            },

            // From SUBMITTED
            self::SUBMITTED => match ($newStatus) {
                self::PENDING_PROCUREMENT => $user->hasAnyRole(['procurement_officer', 'director', 'admin', 'super_admin']),
                self::CANCELLED => $user->hasAnyRole(['site_manager', 'admin', 'super_admin']),
                default => false,
            },

            // From PENDING_PROCUREMENT
            self::PENDING_PROCUREMENT => match ($newStatus) {
                self::PROCUREMENT_PROCESSING => $user->hasAnyRole(['procurement_officer', 'director', 'admin', 'super_admin']),
                self::SUBMITTED => $user->hasAnyRole(['procurement_officer', 'director', 'admin', 'super_admin']), // Send back
                self::CANCELLED => $user->hasAnyRole(['admin', 'super_admin']),
                default => false,
            },

            // From PROCUREMENT_PROCESSING
            self::PROCUREMENT_PROCESSING => match ($newStatus) {
                self::PENDING_DIRECTOR => $user->hasAnyRole(['procurement_officer', 'director', 'admin', 'super_admin']),
                self::PENDING_PROCUREMENT => $user->hasAnyRole(['procurement_officer', 'director', 'admin', 'super_admin']), // Send back
                self::CANCELLED => $user->hasAnyRole(['admin', 'super_admin']),
                default => false,
            },

            // From PENDING_DIRECTOR
            self::PENDING_DIRECTOR => match ($newStatus) {
                self::APPROVED => $user->hasRole('director'),
                self::REJECTED => $user->hasRole('director'),
                self::PROCUREMENT_PROCESSING => $user->hasRole('director'), // Send back for revision
                self::CANCELLED => $user->hasAnyRole(['director', 'admin', 'super_admin']),
                default => false,
            },

            // From APPROVED
            self::APPROVED => match ($newStatus) {
                self::PARTIALLY_DELIVERED => true, // System or Procurement Officer
                self::FULLY_DELIVERED => true, // System or Procurement Officer
                default => false,
            },

            // From PARTIALLY_DELIVERED
            self::PARTIALLY_DELIVERED => match ($newStatus) {
                self::FULLY_DELIVERED => true, // System or Procurement Officer
                default => false,
            },

            // Terminal states (cannot transition)
            self::REJECTED,
            self::CANCELLED,
            self::FULLY_DELIVERED => false,
        };
    }

    /**
     * Get all possible next statuses for this status
     */
    public function possibleNextStatuses(User $user): array
    {
        $possibleStatuses = [];

        foreach (self::cases() as $status) {
            if ($this->canTransitionTo($status, $user)) {
                $possibleStatuses[] = $status;
            }
        }

        return $possibleStatuses;
    }

    /**
     * Check if this is a terminal status
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::CANCELLED,
            self::FULLY_DELIVERED,
        ]);
    }

    /**
     * Check if request can be edited in this status
     */
    public function isEditable(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Check if items can be delivered in this status
     */
    public function canReceiveDelivery(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::PARTIALLY_DELIVERED,
        ]);
    }

    /**
     * Get status by string value (case insensitive)
     */
    public static function fromString(string $value): ?self
    {
        foreach (self::cases() as $status) {
            if (strtolower($status->value) === strtolower($value)) {
                return $status;
            }
        }

        return null;
    }
}