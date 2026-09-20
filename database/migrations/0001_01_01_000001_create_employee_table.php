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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('employee_code', 24)->primary();
            $table->string('store_code')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('position');
            $table->boolean('is_owner')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('store_code')->references('store_code')
                ->on('stores')->restrictOnDelete();
            $table->index(['store_code', 'is_active']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('employee_code');
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->foreign('employee_code')->references('employee_code')->on('employees')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('password_reset_tokens');
    }
};
