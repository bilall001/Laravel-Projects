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
        // Partners Table
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // For login (if needed)
            $table->string('image')->nullable(); // Image file path
            $table->timestamps();
        });

        // Investments Table
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->decimal('contribution', 12, 2);
            $table->string('payment_method')->default('Cash');
            $table->string('payment_receipt')->nullable(); // File path
            $table->date('contribution_date');
            $table->boolean('is_received')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
        Schema::dropIfExists('partners');
    }
};
