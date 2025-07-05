<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            // Link to the parent purchase order
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');

            // Link to the product
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Order details
            $table->decimal('quantity_kg', 10, 2); // Quantity in kilograms
            $table->date('required_delivery_date'); // Required delivery date for this line item
            $table->decimal('unit_price_ex_vat', 10, 2); // Price per KG (ex VAT) at time of order
            $table->decimal('total_value_ex_vat', 12, 2); // Calculated: quantity_kg * unit_price_ex_vat

            // Delivery tracking
            $table->decimal('delivered_quantity_kg', 10, 2)->nullable(); // Actual delivered quantity
            $table->date('actual_delivery_date')->nullable(); // When it was actually delivered

            // Quality control
            $table->enum('quality_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('quality_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['purchase_order_id', 'product_id']);
            $table->index('required_delivery_date');
            $table->index('quality_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};