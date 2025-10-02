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
        Schema::create('points', function (Blueprint $table) {
     $table->id();
    $table->foreignId('developer_id')->constrained('add_users')->onDelete('cascade');
    $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
    $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
    $table->string('video_link')->nullable();
    $table->string('video_file')->nullable();
    $table->integer('points');
    $table->timestamp('uploaded_at')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};