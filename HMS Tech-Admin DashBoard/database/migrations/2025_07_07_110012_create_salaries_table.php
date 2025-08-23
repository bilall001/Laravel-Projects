<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();

            // Foreign key to add_users table
            $table->foreignId('add_user_id')
                  ->constrained('add_users')
                  ->onDelete('cascade');

            $table->date('salary_date');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['Cash', 'Account']);
            $table->string('payment_receipt')->nullable();
            $table->boolean('is_paid')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salaries', function (Blueprint $table) {
            // Drop foreign key explicitly before dropping the table
            $table->dropForeign(['add_user_id']);
        });

        Schema::dropIfExists('salaries');
    }
}
