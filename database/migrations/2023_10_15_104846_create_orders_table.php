<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            $table->float('amount');
            $table->string('amount_pay_type');
            $table->float('discount')->nullable();
            $table->float('amount_after_discount')->nullable();
            $table->float('tax');
            $table->float('employee_commission');
            $table->float('manager_commission')->nullable();
            $table->float('representative_commission')->nullable();
            $table->float('tip')->nullable();
            $table->string('tip_pay_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
