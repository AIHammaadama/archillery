<?php

namespace App\Policies;

use App\Models\ProcurementRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcurementRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'view-requests',
            'manage-requests',
            'approve-requests',
            'create-purchase-request'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Users with 'view-all-requests' can see everything
        if ($user->hasPermission('view-all-requests')) {
            return true;
        }

        // Site Managers: only their own requests
        if ($user->hasPermission('create-purchase-request') && !$user->hasPermission('process-purchase-request')) {
            return $procurementRequest->requested_by === $user->id;
        }

        // Procurement Officers: only requests from their assigned projects
        if ($user->hasPermission('process-purchase-request')) {
            return $procurementRequest->procurement_officer_id === $user->id ||
                $procurementRequest->project->isAssignedTo($user, 'procurement_officer');
        }

        return false;
    }

    /**
     * CRITICAL: Determine if user can view pricing information
     * Site Managers CANNOT see prices or vendors
     */
    public function viewPricing(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Users without 'view-request-pricing' permission cannot see pricing
        if (!$user->hasPermission('view-request-pricing')) {
            return false;
        }

        // Users with the permission can see pricing
        return true;
    }

    /**
     * Determine if user can view vendor information
     */
    public function viewVendors(User $user): bool
    {
        // Same as pricing - users need 'view-vendors' permission
        return $user->hasPermission('view-vendors');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-purchase-request');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Only in draft status
        if (!$procurementRequest->isEditable()) {
            return false;
        }

        // Only the requester can update their draft
        return $procurementRequest->requested_by === $user->id;
    }

    /**
     * Determine if user can assign vendors to items
     */
    public function assignVendors(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Must have the assign-vendors permission
        if (!$user->hasPermission('assign-vendors')) {
            return false;
        }

        // Users with 'view-all-requests' can assign vendors to any request
        if ($user->hasPermission('view-all-requests')) {
            return true;
        }

        // Procurement Officers can assign vendors to:
        // 1. Requests assigned to them directly
        // 2. Requests from projects they are assigned to
        if ($user->hasPermission('process-purchase-request')) {
            return $procurementRequest->procurement_officer_id === $user->id ||
                   optional($procurementRequest->project)->isAssignedTo($user, 'procurement_officer');
        }

        return false;
    }

    /**
     * Determine if user can approve the request
     */
    public function approve(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Must have permission AND request must be in correct status
        return $user->hasPermission('approve-purchase-request') &&
            in_array($procurementRequest->status->value, ['pending_director']);
    }

    /**
     * Determine if user can reject the request
     */
    public function reject(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Must have permission AND request must be in correct status
        return $user->hasPermission('reject-purchase-request') &&
            in_array($procurementRequest->status->value, ['pending_director']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProcurementRequest $procurementRequest): bool
    {
        // Only drafts can be deleted, only by the creator or users with delete permission
        if (!$procurementRequest->isEditable()) {
            return false;
        }

        return $procurementRequest->requested_by === $user->id ||
            $user->hasPermission('delete-purchase-request');
    }

    /**
     * Admin only actions
     */
    public function restore(User $user, ProcurementRequest $procurementRequest): bool
    {
        return $user->hasPermission('delete-purchase-request');
    }

    public function forceDelete(User $user, ProcurementRequest $procurementRequest): bool
    {
        return $user->hasPermission('delete-purchase-request');
    }
}