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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code', 12);
            $table->string('sku', 12);
            $table->decimal('quantity', 15);
            $table->decimal('unit_price', 15);
            $table->timestamps();

            $table->foreign('purchase_code')->references('purchase_code')->on('purchases');
            $table->foreign('sku')->references('sku')->on('products');
            $table->index(['purchase_code', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
