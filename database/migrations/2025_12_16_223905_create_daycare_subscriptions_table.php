<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daycare_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('daycare_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade'); // المشرف
            $table->date('start_date'); // تاريخ البداية
            $table->date('end_date'); // تاريخ النهاية
            $table->decimal('price', 10, 2); // السعر
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daycare_subscriptions');
    }
};
