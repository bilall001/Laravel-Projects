<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('khata_entries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('khata_account_id');
            $table->date('entry_date');

            $table->enum('ref_type', [
                'invoice','payment','expense','salary','investment','adjustment','opening','other'
            ])->default('other');
            $table->unsignedBigInteger('ref_id')->nullable();     // polymorphic id (no FK)
            $table->unsignedBigInteger('project_id')->nullable(); // optional tag to projects

            $table->text('description')->nullable();

            $table->decimal('debit', 14, 2)->default(0);          // increases party owes you
            $table->decimal('credit', 14, 2)->default(0);         // decreases party owes you
            $table->decimal('running_balance', 14, 2)->default(0);

            $table->enum('payment_method', ['none','cash','online'])->default('none');
            $table->string('online_reference', 120)->nullable();
            $table->string('online_proof_path', 255)->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['khata_account_id','entry_date','id'], 'idx_khata_date');
            $table->index(['owner_id','entry_date'], 'idx_owner_date');
            $table->index(['ref_type','ref_id'], 'idx_ref');
            $table->index('project_id', 'idx_project');

            $table->foreign('owner_id')->references('id')->on('add_users')->onDelete('cascade');
            $table->foreign('khata_account_id')->references('id')->on('khata_accounts')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('add_users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('khata_entries');
    }
};
