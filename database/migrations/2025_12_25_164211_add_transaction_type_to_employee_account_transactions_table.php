<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_account_transactions', function (Blueprint $table) {
            $table->string('transaction_type')->nullable()->after('type'); // advance, bonus, deduction, salary
        });

        // تحديث البيانات الموجودة
        DB::table('employee_account_transactions')
            ->where('type', 'credit')
            ->whereNull('transaction_type')
            ->update(['transaction_type' => 'advance']);

        DB::table('employee_account_transactions')
            ->where('type', 'debit')
            ->whereNull('transaction_type')
            ->update(['transaction_type' => 'deduction']);
    }

    public function down(): void
    {
        Schema::table('employee_account_transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
