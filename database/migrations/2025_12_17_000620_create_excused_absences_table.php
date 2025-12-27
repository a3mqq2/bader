<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excused_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['sessions', 'daycare']); // نوع الغياب: جلسات أو رعاية نهارية
            $table->date('start_date'); // من تاريخ
            $table->date('end_date'); // إلى تاريخ
            $table->enum('reason', ['illness', 'travel', 'family', 'other']); // السبب
            $table->text('reason_details')->nullable(); // تفاصيل السبب (للسبب الآخر)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // فهرس للبحث السريع
            $table->index(['student_id', 'type', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excused_absences');
    }
};
