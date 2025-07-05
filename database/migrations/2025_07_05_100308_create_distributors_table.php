<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->text('registered_address');
            $table->string('country');
            $table->string('vat_number');
            $table->string('primary_sales_contact_name');
            $table->string('primary_sales_contact_email');
            $table->string('primary_sales_contact_telephone');
            $table->string('primary_logistics_contact_name');
            $table->string('primary_logistics_contact_email');
            $table->string('primary_logistics_contact_telephone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};
