<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Dashboard & General
            ['name' => 'Access Admin Dashboard', 'slug' => 'access-admin-dashboard', 'group' => 'General'],
            ['name' => 'View Notifications', 'slug' => 'view-notifications', 'group' => 'General'],

            // Projects
            ['name' => 'View Projects', 'slug' => 'view-projects', 'group' => 'Projects'],
            ['name' => 'View All Projects', 'slug' => 'view-all-projects', 'group' => 'Projects'],
            ['name' => 'Create Projects', 'slug' => 'create-projects', 'group' => 'Projects'],
            ['name' => 'Edit Projects', 'slug' => 'edit-projects', 'group' => 'Projects'],
            ['name' => 'Delete Projects', 'slug' => 'delete-projects', 'group' => 'Projects'],
            ['name' => 'Assign Project Users', 'slug' => 'assign-project-users', 'group' => 'Projects'],

            // Procurement Requests
            ['name' => 'View Requests', 'slug' => 'view-requests', 'group' => 'Requests'],
            ['name' => 'View All Requests', 'slug' => 'view-all-requests', 'group' => 'Requests'],
            ['name' => 'View Request Pricing', 'slug' => 'view-request-pricing', 'group' => 'Requests'],
            ['name' => 'Create Purchase Request', 'slug' => 'create-purchase-request', 'group' => 'Requests'],
            ['name' => 'Edit Purchase Request', 'slug' => 'edit-purchase-request', 'group' => 'Requests'],
            ['name' => 'Delete Purchase Request', 'slug' => 'delete-purchase-request', 'group' => 'Requests'],
            ['name' => 'Process Purchase Request', 'slug' => 'process-purchase-request', 'group' => 'Requests'],
            ['name' => 'Approve Purchase Request', 'slug' => 'approve-purchase-request', 'group' => 'Requests'],
            ['name' => 'Reject Purchase Request', 'slug' => 'reject-purchase-request', 'group' => 'Requests'],
            ['name' => 'Send Back Request for Revision', 'slug' => 'send-back-request', 'group' => 'Requests'],

            // Vendors
            ['name' => 'View Vendors', 'slug' => 'view-vendors', 'group' => 'Vendors'],
            ['name' => 'Create Vendors', 'slug' => 'create-vendors', 'group' => 'Vendors'],
            ['name' => 'Edit Vendors', 'slug' => 'edit-vendors', 'group' => 'Vendors'],
            ['name' => 'Delete Vendors', 'slug' => 'delete-vendors', 'group' => 'Vendors'],
            ['name' => 'Manage Vendors', 'slug' => 'manage-vendors', 'group' => 'Vendors'],
            ['name' => 'Assign Vendors', 'slug' => 'assign-vendors', 'group' => 'Vendors'],

            // Materials
            ['name' => 'View Materials', 'slug' => 'view-materials', 'group' => 'Materials'],
            ['name' => 'Create Materials', 'slug' => 'create-materials', 'group' => 'Materials'],
            ['name' => 'Edit Materials', 'slug' => 'edit-materials', 'group' => 'Materials'],
            ['name' => 'Delete Materials', 'slug' => 'delete-materials', 'group' => 'Materials'],
            ['name' => 'Manage Materials', 'slug' => 'manage-materials', 'group' => 'Materials'],

            // Deliveries
            ['name' => 'View Deliveries', 'slug' => 'view-deliveries', 'group' => 'Deliveries'],
            ['name' => 'Record Deliveries', 'slug' => 'record-deliveries', 'group' => 'Deliveries'],
            ['name' => 'Verify Deliveries', 'slug' => 'verify-deliveries', 'group' => 'Deliveries'],
            ['name' => 'Edit Deliveries', 'slug' => 'edit-deliveries', 'group' => 'Deliveries'],
            ['name' => 'Delete Deliveries', 'slug' => 'delete-deliveries', 'group' => 'Deliveries'],

            // Reports
            ['name' => 'View Procurement Reports', 'slug' => 'view-procurement-reports', 'group' => 'Reports'],
            ['name' => 'Generate Reports', 'slug' => 'generate-reports', 'group' => 'Reports'],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'group' => 'Reports'],

            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'group' => 'User Management'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'group' => 'User Management'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'group' => 'User Management'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'group' => 'User Management'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'group' => 'User Management'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'group' => 'User Management'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'group' => 'User Management'],
            ['name' => 'Assign Roles', 'slug' => 'assign-roles', 'group' => 'User Management'],

            // Audit
            ['name' => 'View Audits', 'slug' => 'view-audits', 'group' => 'Audit'],
            ['name' => 'Manage Audits', 'slug' => 'manage-audits', 'group' => 'Audit'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}