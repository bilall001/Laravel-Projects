<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('developers', function (Blueprint $table) {
               $table->id();
        $table->foreignId('add_user_id')->constrained('add_users')->onDelete('cascade');
        $table->string('skill')->nullable();
        $table->string('experience')->nullable();
        $table->boolean('part_time')->default(false);
        $table->boolean('full_time')->default(false);
        $table->boolean('internship')->default(false);
        $table->boolean('job')->default(false);
        $table->decimal('salary', 10, 2)->nullable();
        $table->string('profile_image')->nullable();
        $table->string('cnic_front')->nullable();
        $table->string('cnic_back')->nullable();
        $table->string('contract_file')->nullable();
        $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('developers');
    }
};