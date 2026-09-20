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
        Schema::create('employee_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 24);
            $table->foreignId('permission_id')->constrained('permissions')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('employee_code')->references('employee_code')->on('employees')
                ->cascadeOnDelete();

            $table->unique(['employee_code', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_permissions');
    }
};
