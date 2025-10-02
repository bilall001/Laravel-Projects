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
        Schema::create('partner_profits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_profit_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('profit_amount', 15, 2)->default(0);
            $table->boolean('is_received')->default(false);
            $table->boolean('reinvested')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_profits');
    }
};
