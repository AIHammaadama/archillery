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
        Schema::create('request_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('procurement_requests')->onDelete('cascade');
            $table->string('from_status');
            $table->string('to_status');
            $table->foreignUuid('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index(['request_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_status_histories');
    }
};
