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
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('procurement_requests')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit_of_measurement', 50)->nullable();

            $table->decimal('estimated_unit_price', 15, 2)->nullable();
            $table->decimal('quoted_unit_price', 15, 2)->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');

            $table->decimal('quantity_delivered', 10, 2)->default(0.00);
            $table->enum('delivery_status', ['pending', 'partial', 'complete'])->default('pending');

            $table->text('specifications')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('request_id');
            $table->index('material_id');
            $table->index('vendor_id');
            $table->index('delivery_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_items');
    }
};
