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
        Schema::create('company_expenses', function (Blueprint $table) {
              $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->decimal('amount', 10, 2);
        $table->string('currency', 3)->default('USD');
        $table->string('category')->nullable();
        $table->date('date');
        $table->string('receipt_file')->nullable();
        $table->string('created_by')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_expenses');
    }
};