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
        // Create a standard index on project_id first, as it's needed for the foreign key
        // and currently the unique index is likely serving that purpose.
        try {
            DB::statement("CREATE INDEX project_assignments_project_id_index ON project_assignments (project_id)");
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        // Now force drop the unique index.
        DB::statement("ALTER TABLE project_assignments DROP INDEX unique_project_role_active");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_assignments', function (Blueprint $table) {
            $table->unique(['project_id', 'role_type', 'is_active'], 'unique_project_role_active');
        });
    }
};
