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
        Schema::table('company_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('developer_payment_id')->nullable()->after('salary_id');

            // Ensure 1 expense per developer payment
            $table->unique('developer_payment_id', 'uniq_company_expenses_dev_payment');

            $table->foreign('developer_payment_id')
                ->references('id')
                ->on('developer_project_payments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_expenses', function (Blueprint $table) {
            $table->dropForeign(['developer_payment_id']);
            $table->dropUnique('uniq_company_expenses_dev_payment');
            $table->dropColumn('developer_payment_id');
        });
    }
};
