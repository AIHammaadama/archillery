<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define permissions for each role
        $rolePermissions = [
            'site_manager' => [
                'access-admin-dashboard',
                'view-notifications',
                'view-projects',
                'view-requests',
                'create-purchase-request',
                'edit-purchase-request',
                'delete-purchase-request',
                'view-materials',
                'view-deliveries',
                'record-deliveries',
            ],
            'procurement_officer' => [
                'access-admin-dashboard',
                'view-notifications',
                'view-projects',
                'view-all-projects',
                'view-requests',
                'view-all-requests',
                'view-request-pricing',
                'process-purchase-request',
                'view-vendors',
                'create-vendors',
                'edit-vendors',
                'manage-vendors',
                'assign-vendors',
                'view-materials',
                'view-deliveries',
                'record-deliveries',
                'verify-deliveries',
                'edit-deliveries',
                'view-procurement-reports',
                'generate-reports',
            ],
            'director' => [
                'access-admin-dashboard',
                'view-notifications',
                'view-projects',
                'view-all-projects',
                'create-projects',
                'edit-projects',
                'assign-project-users',
                'view-requests',
                'view-all-requests',
                'view-request-pricing',
                'process-purchase-request',
                'approve-purchase-request',
                'reject-purchase-request',
                'send-back-request',
                'assign-vendors',
                'view-vendors',
                'view-materials',
                'view-deliveries',
                'verify-deliveries',
                'view-procurement-reports',
                'generate-reports',
                'export-reports',
            ],
            'admin' => [
                'access-admin-dashboard',
                'view-notifications',
                'view-projects',
                'view-all-projects',
                'create-projects',
                'edit-projects',
                'delete-projects',
                'assign-project-users',
                'view-requests',
                'view-all-requests',
                'view-request-pricing',
                'create-purchase-request',
                'edit-purchase-request',
                'delete-purchase-request',
                'process-purchase-request',
                'approve-purchase-request',
                'reject-purchase-request',
                'send-back-request',
                'assign-vendors',
                'view-vendors',
                'create-vendors',
                'edit-vendors',
                'delete-vendors',
                'manage-vendors',
                'view-materials',
                'create-materials',
                'edit-materials',
                'delete-materials',
                'manage-materials',
                'view-deliveries',
                'record-deliveries',
                'verify-deliveries',
                'edit-deliveries',
                'delete-deliveries',
                'view-procurement-reports',
                'generate-reports',
                'export-reports',
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'manage-users',
                'manage-roles',
                'manage-permissions',
                'assign-roles',
                'view-audits',
                'manage-audits',
            ],
            'super_admin' => Permission::all()->pluck('slug')->toArray(),
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) {
                continue;
            }

            if (is_array($permissionSlugs)) {
                $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
                $role->permissions()->sync($permissions->pluck('id'));
            }
        }
    }
}