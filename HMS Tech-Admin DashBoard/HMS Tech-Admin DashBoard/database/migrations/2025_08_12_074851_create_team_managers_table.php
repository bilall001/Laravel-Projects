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
        Schema::create('team_managers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();
          

            $table->string('phone')->nullable();
            $table->string('skill1')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('experience',255)->nullable();
            $table->string('contract_file')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('add_users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_managers');
    }
};
