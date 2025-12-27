<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('treasury_id')->nullable()->after('invoice_id')->constrained()->onDelete('set null');
            $table->foreignId('transaction_id')->nullable()->after('treasury_id')->constrained('financial_transactions')->onDelete('set null');
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('account_number')->nullable()->after('bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['treasury_id']);
            $table->dropForeign(['transaction_id']);
            $table->dropColumn(['treasury_id', 'transaction_id', 'bank_name', 'account_number']);
        });
    }
};
