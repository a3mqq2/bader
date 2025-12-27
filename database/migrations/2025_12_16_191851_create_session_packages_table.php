<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول باقات الجلسات
        Schema::create('session_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('therapy_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialist_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('session_time');
            $table->integer('session_duration')->default(30); // بالدقائق
            $table->json('days'); // أيام الأسبوع ['saturday', 'sunday', ...]
            $table->decimal('total_price', 10, 2)->default(0);
            $table->integer('sessions_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // جدول الجلسات الفردية
        Schema::create('student_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialist_id')->constrained('users')->onDelete('cascade');
            $table->date('session_date');
            $table->time('session_time');
            $table->integer('duration')->default(30);
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'absent'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sessions');
        Schema::dropIfExists('session_packages');
    }
};
