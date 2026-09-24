<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->string('purchase_code', 12)->primary();
            $table->string('supplier_receipt_number')->unique();
            $table->string('supplier_code', 12)->nullable();
            $table->string('store_code');
            $table->string('employee_code', 24);
            $table->date('purchase_date');
            $table->string('payment_method');
            $table->timestamps();

            $table->foreign('supplier_code')->references('supplier_code')->on('suppliers');
            $table->foreign('store_code')->references('store_code')->on('stores');
            $table->foreign('employee_code')->references('employee_code')->on('employees');
            $table->index(['store_code', 'purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
