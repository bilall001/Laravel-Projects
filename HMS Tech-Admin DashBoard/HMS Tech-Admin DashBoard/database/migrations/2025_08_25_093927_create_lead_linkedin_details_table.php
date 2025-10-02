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
        Schema::create('lead_linkedin_details', function (Blueprint $table) {
            $table->id();
             $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');

            $table->string('company_name')->nullable();
            $table->string('profile_link')->nullable();
            $table->string('job_post_url')->nullable();
            $table->text('message_sent')->nullable(); // e.g. connection request or DM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_linkedin_details');
    }
};
