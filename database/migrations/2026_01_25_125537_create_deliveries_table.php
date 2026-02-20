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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number', 50)->unique();
            $table->foreignId('request_id')->constrained('procurement_requests')->onDelete('cascade');
            $table->foreignId('request_item_id')->nullable()->constrained('request_items')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');

            $table->date('delivery_date');
            $table->decimal('quantity_delivered', 10, 2);

            $table->string('received_by')->nullable();
            $table->string('verified_by')->nullable();
            $table->enum('verification_status', ['pending', 'accepted', 'rejected', 'partial'])->default('pending');
            $table->text('quality_notes')->nullable();

            $table->string('waybill_number', 100)->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();
            $table->json('attachments')->nullable();

            $table->timestamps();

            $table->index('delivery_number');
            $table->index('request_id');
            $table->index('delivery_date');
            $table->index('verification_status');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};
