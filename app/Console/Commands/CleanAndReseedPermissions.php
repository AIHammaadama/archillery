<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolePermissionSeeder;

class CleanAndReseedPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:clean-and-reseed {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all permission-related tables and reseed with enhanced permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete all existing permission assignments. Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return Command::FAILURE;
            }
        }

        $this->info('Starting permission cleanup and reseed process...');

        try {
            // Step 1: Delete all permission_role assignments
            $this->info('Cleaning permission_role table...');
            DB::table('permission_role')->delete();

            // Step 2: Delete all permission_user assignments (if table exists)
            if (DB::getSchemaBuilder()->hasTable('permission_user')) {
                $this->info('Cleaning permission_user table...');
                DB::table('permission_user')->delete();
            }

            // Step 3: Delete all existing permissions (they will be recreated)
            $this->info('Deleting existing permissions...');
            DB::table('permissions')->delete();

            $this->info('Cleanup completed successfully!');
            $this->newLine();

            // Step 4: Run PermissionsSeeder
            $this->info('Seeding permissions...');
            $permissionsSeeder = new PermissionsSeeder();
            $permissionsSeeder->run();
            $this->info('✓ Permissions seeded successfully!');
            $this->newLine();

            // Step 5: Run RolePermissionSeeder
            $this->info('Assigning permissions to roles...');
            $rolePermissionSeeder = new RolePermissionSeeder();
            $rolePermissionSeeder->run();
            $this->info('✓ Role permissions assigned successfully!');
            $this->newLine();

            // Display summary
            $permissionCount = DB::table('permissions')->count();
            $assignmentCount = DB::table('permission_role')->count();

            $this->info('═══════════════════════════════════════');
            $this->info('         SUMMARY');
            $this->info('═══════════════════════════════════════');
            $this->info("Total Permissions Created: {$permissionCount}");
            $this->info("Total Role-Permission Assignments: {$assignmentCount}");
            $this->info('═══════════════════════════════════════');
            $this->newLine();

            $this->info('✅ Permission system has been successfully cleaned and reseeded!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error occurred during cleanup and reseed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
