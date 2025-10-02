<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('khata_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id'); // admin who owns this private khata

            $table->string('name'); // used for manual/other; also shown as fallback label

            $table->enum('party_type', [
                'clients', 'partners', 'developers', 'team_managers', 'business_developers', 'manual', 'other'
            ])->default('manual');

            $table->unsignedBigInteger('party_id')->nullable(); // id in source table (NULL for manual/other)

            // Optional contact fields for manual/other
            $table->string('phone', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('cnic', 30)->nullable();
            $table->string('address', 255)->nullable();

            $table->decimal('opening_balance', 14, 2)->default(0); // + = they owe you; - = you owe them
            $table->char('currency', 3)->default('PKR');
            $table->enum('status', ['active','archived'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('owner_id', 'idx_khata_accounts_owner');
            // one khata per party (per owner). For manual/other, party_id is NULL so duplicates allowed.
            $table->unique(['owner_id','party_type','party_id'], 'uq_owner_party');

            // adjust FK if your auth table is `users`
            $table->foreign('owner_id')->references('id')->on('add_users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('khata_accounts');
    }
};
