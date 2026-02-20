<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Directors and Admins see all projects, others see only assigned projects
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-projects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Users with 'view-all-projects' permission can see all projects
        if ($user->hasPermission('view-all-projects')) {
            return true;
        }

        // Users with 'view-projects' can only see assigned projects
        if ($user->hasPermission('view-projects')) {
            return $project->isAssignedTo($user);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-projects');
    }

    /**
     * Determine whether the user can assign users to projects.
     */
    public function assignUsers(User $user, Project $project): bool
    {
        return $user->hasPermission('assign-project-users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('edit-projects');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only allow deletion if project has no procurement requests
        if ($project->procurementRequests()->count() > 0) {
            return false;
        }

        return $user->hasPermission('delete-projects');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasPermission('delete-projects');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasPermission('delete-projects');
    }
}
