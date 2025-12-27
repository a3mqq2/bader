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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treasury_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('transaction_categories')->onDelete('restrict');
            $table->enum('type', ['income', 'expense']); // إيراد أو مصروف
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('document_number')->nullable();
            $table->string('recipient_name')->nullable();
            $table->decimal('balance_after', 12, 2); // رصيد الخزينة بعد الحركة
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['treasury_id', 'created_at']);
            $table->index(['category_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
