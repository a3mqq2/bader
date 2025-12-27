<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daycare_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('daycare_subscriptions')->onDelete('cascade');
            $table->date('date'); // تاريخ اليوم
            $table->enum('status', ['present', 'absent'])->default('absent'); // حاضر / غائب
            $table->text('notes')->nullable();
            $table->timestamps();

            // منع تكرار نفس اليوم لنفس الاشتراك
            $table->unique(['subscription_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daycare_attendances');
    }
};
