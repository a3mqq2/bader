<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة حقول الحساب المصرفي للموظفين
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('salary');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });

        // إضافة حقول طريقة الدفع لحركات حسابات الموظفين
        Schema::table('employee_account_transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('description'); // cash, bank_transfer
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_name']);
        });

        Schema::table('employee_account_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'bank_name', 'bank_account_number', 'bank_account_name']);
        });
    }
};
