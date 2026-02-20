<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // RBAC Setup
            RolesSeeder::class,
            PermissionsSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,

            // Geographic Data
            StatesLgasSeeder::class,

            // Procurement Setup
            MaterialsSeeder::class,
            VendorsSeeder::class,
        ]);
    }
}