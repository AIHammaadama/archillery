<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->enum('site_manager_status', ['pending', 'received', 'issues_noted', 'completed'])->default('pending')->after('verification_status');
            $table->text('site_manager_comments')->nullable()->after('site_manager_status');
            $table->string('site_manager_updated_by')->nullable()->after('site_manager_comments');
            $table->timestamp('site_manager_updated_at')->nullable()->after('site_manager_updated_by');

            $table->foreign('site_manager_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['site_manager_updated_by']);
            $table->dropColumn(['site_manager_status', 'site_manager_comments', 'site_manager_updated_by', 'site_manager_updated_at']);
        });
    }
};
