<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // PO number with prefix (POD- or POS-)
            $table->string('po_number')->unique();

            // Type of purchase order
            $table->enum('type', ['distributor_order', 'supplier_order']);

            // Foreign keys - nullable because a PO is either from distributor OR to supplier
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('distributor_id')->nullable()->constrained()->onDelete('cascade');

            // Purchase order status
            $table->enum('status', [
                'new',
                'accepted_by_supplier',
                'in_delivery',
                'delivered',
                'rejected_by_supplier',
                'rejected_by_distributor',
                'cancelled'
            ])->default('new');

            // Link POD to POS - when a Field Sales Associate creates a POS for a POD
            $table->foreignId('linked_po_id')->nullable()->constrained('purchase_orders')->onDelete('set null');

            // Who created this PO
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Additional business fields
            $table->text('notes')->nullable();
            $table->date('required_delivery_date')->nullable();
            $table->decimal('total_value_ex_vat', 12, 2)->default(0);

            $table->timestamps();

            // Indexes for performance
            $table->index(['type', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index(['distributor_id', 'status']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};