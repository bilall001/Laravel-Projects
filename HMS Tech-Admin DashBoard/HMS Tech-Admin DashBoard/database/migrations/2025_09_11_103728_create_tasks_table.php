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
        Schema::create('tasks', function (Blueprint $table) {
             $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('body_html')->nullable();  // rendered HTML from editor
            $table->longText('body_json')->nullable();  // optional raw JSON/delta
            $table->date('due_date')->nullable();

            $table->enum('status', ['pending', 'in_progress', 'review', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // who created the task (optional)
            $table->foreignId('created_by')->nullable()->constrained('add_users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
