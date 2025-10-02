<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_developers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('add_user_id'); // FK to add_users table
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // Example: 12.50
            $table->string('image')->nullable();
            $table->string('cnic_front')->nullable();
            $table->string('cnic_back')->nullable();
            $table->timestamps();

            $table->foreign('add_user_id')
                ->references('id')
                ->on('add_users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_developers');
    }
};
