<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('treasury_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('financial_transaction_id')->nullable()->constrained('financial_transactions')->onDelete('set null');
            $table->enum('type', ['credit', 'debit']); // credit = صرف للموظف, debit = خصم من الموظف
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2); // رصيد الموظف بعد الحركة
            $table->string('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // إضافة عمود الرصيد الحالي للمستخدم
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('account_balance', 12, 2)->default(0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_account_transactions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_balance');
        });
    }
};
