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
        Schema::table('tasks', function (Blueprint $table) {
            // Drop the old FK constraint
            $table->dropForeign(['user_id']);

            // Add FK to add_users table
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
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};