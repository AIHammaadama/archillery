<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('slug', 'super_admin')->first();

        $user = User::firstOrCreate(
            ['email' => 'admin@ppms.local'],
            [
                'id' => (string) Str::uuid(),
                'firstname' => 'System',
                'lastname' => 'Administrator',
                'password' => Hash::make('password123'),
                'status' => 1,
                'role_id' => $role->id,
            ]
        );

        // Update role_id if user already exists
        if (!$user->role_id) {
            $user->update(['role_id' => $role->id]);
        }
    }
}
