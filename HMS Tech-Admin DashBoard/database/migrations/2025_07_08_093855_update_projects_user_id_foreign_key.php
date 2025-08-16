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
        Schema::table('projects', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['user_id']);

            // Add the new foreign key to add_users
            $table->foreign('user_id')
                ->references('id')
                ->on('add_users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
      public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Roll back to users table if needed
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};