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
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('requested_by');
            $table->date('request_date');
            $table->date('required_by_date')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'pending_procurement',
                'procurement_processing',
                'pending_director',
                'approved',
                'rejected',
                'cancelled',
                'partially_delivered',
                'fully_delivered'
            ])->default('draft');

            $table->string('procurement_officer_id')->nullable();
            $table->timestamp('assigned_at')->nullable();

            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->decimal('total_estimated_amount', 15, 2)->default(0.00);
            $table->decimal('total_quoted_amount', 15, 2)->default(0.00);

            $table->text('justification')->nullable();
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('request_number');
            $table->index('project_id');
            $table->index('status');
            $table->index('requested_by');
            $table->index('request_date');
            $table->index('procurement_officer_id');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('procurement_officer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procurement_requests');
    }
};
