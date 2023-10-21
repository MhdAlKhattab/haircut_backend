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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();

            $table->string('name');
            
            $table->string('residence_number');
            $table->date('residence_expire_date');
            $table->string('health_number');
            $table->date('health_expire_date');

            $table->string('job');
            $table->string('pay_type');
            $table->float('salary');

            $table->float('income_limit')->default(-1);
            $table->float('commission')->default(0);

            $table->float('residence_cost');
            $table->float('health_cost');
            $table->float('insurance_cost');

            $table->string('costs_responsible');

            $table->boolean('state')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
