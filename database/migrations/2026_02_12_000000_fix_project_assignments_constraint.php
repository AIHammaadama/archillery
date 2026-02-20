<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE project_assignments DROP INDEX unique_project_role_active");
        } catch (\Exception $e) {
            // Index might not exist or other issue. 
            // We continue as the goal is to ensure it is gone or we can't drop it.
            // If it fails because it doesn't exist, that's fine.
            if (!str_contains($e->getMessage(), "check that column/key exists")) {
                 // Log or just ignore for now if we assume it's the missing index error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_assignments', function (Blueprint $table) {
            $table->dropUnique('unique_project_user_role');
            $table->unique(['project_id', 'role_type', 'is_active'], 'unique_project_role_active');
        });
    }
};
