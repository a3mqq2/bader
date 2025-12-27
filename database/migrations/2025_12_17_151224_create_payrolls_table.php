<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول كشف المرتبات الرئيسي (لكل شهر)
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->enum('status', ['draft', 'executed'])->default('draft');
            $table->foreignId('treasury_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('executed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });

        // جدول تفاصيل مرتبات الموظفين
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->integer('work_days')->default(0);
            $table->decimal('work_hours', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            $table->unique(['payroll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payrolls');
    }
};
