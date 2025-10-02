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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'paid_price')) {
                $table->decimal('paid_price', 10, 2)->nullable()->default(0);
            }

            if (!Schema::hasColumn('projects', 'remaining_price')) {
                $table->decimal('remaining_price', 10, 2)->nullable()->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
      public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'paid_price')) {
                $table->dropColumn('paid_price');
            }
            if (Schema::hasColumn('projects', 'remaining_price')) {
                $table->dropColumn('remaining_price');
            }
        });
    }
};