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
        Schema::create('lead_other_details', function (Blueprint $table) {
            $table->id();
              $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');

            // General fields for any other platform
            $table->string('platform_name')->nullable(); // e.g. Twitter, WhatsApp, Google Ads
            $table->string('platform_url')->nullable(); // URL if available (ad link, profile link, etc.)
            $table->string('campaign_name')->nullable(); // e.g. "Google Ads - Summer Campaign"
            $table->text('inquiry_message')->nullable(); // Client's message or inquiry
            $table->decimal('estimated_budget', 12, 2)->nullable(); // optional budget info
            $table->string('contact_method')->nullable(); // e.g. "Email", "Phone", "DM"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_other_details');
    }
};
