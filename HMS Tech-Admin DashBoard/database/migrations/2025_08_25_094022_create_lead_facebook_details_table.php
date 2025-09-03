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
        Schema::create('lead_facebook_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');

            $table->string('page_name')->nullable();
            $table->string('ad_campaign_name')->nullable();
            $table->string('post_url')->nullable();
            $table->text('inquiry_message')->nullable(); // client’s message or ad inquiry
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_facebook_details');
    }
};
