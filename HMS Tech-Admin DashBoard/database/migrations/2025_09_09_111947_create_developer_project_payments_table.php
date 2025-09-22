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
        Schema::create('developer_project_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('developers')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // payment details
            $table->enum('payment_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('amount', 10, 2); // 5000 for fixed, 20 for percentage
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_project_payments');
    }
};
