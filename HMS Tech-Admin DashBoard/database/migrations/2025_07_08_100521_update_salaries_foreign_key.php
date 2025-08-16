<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('salaries', function (Blueprint $table) {
            // Drop the old FK
            $table->dropForeign(['developer_id']);

            // Add the correct FK to add_users
            $table->foreign('developer_id')
                  ->references('id')
                  ->on('add_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropForeign(['developer_id']);
            $table->foreign('developer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};