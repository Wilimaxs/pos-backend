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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 12);
            $table->string('store_code')->index();
            $table->decimal('stock_minimum')->default(0);
            $table->decimal('stock_quantity')->default(0);
            $table->decimal('selling_price')->default(0);
            $table->timestamps();

            $table->foreign('sku')->references('sku')->on('products');
            $table->foreign('store_code')->references('store_code')->on('stores');
            $table->unique(['sku', 'store_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
