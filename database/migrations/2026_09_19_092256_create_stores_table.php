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
        Schema::create('stores', function (Blueprint $table) {
            $table->string('store_code')->primary();
            $table->string('name')->index();
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->enum('type', ['CENTRAL', 'BRANCH'])->default('BRANCH')->comment('CENTRAL, BRANCH');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
