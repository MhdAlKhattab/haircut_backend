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
            $table->float('discount')->default(0);
            $table->float('amount_after_discount')->default(-1);
            $table->float('tax');
            $table->float('employee_commission');
            $table->float('manager_commission')->default(0);
            $table->float('representative_commission')->default(0);
            $table->float('tip')->default(0);
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
