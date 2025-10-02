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
        Schema::create('lead_fiverr_details', function (Blueprint $table) {
            $table->id();
              $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');

            $table->string('gig_title')->nullable();
            $table->text('buyer_request_message')->nullable();
            $table->decimal('offer_amount', 12, 2)->nullable();
            $table->string('buyer_username')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_fiverr_details');
    }
};
