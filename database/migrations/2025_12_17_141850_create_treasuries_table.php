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
        Schema::create('treasuries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // جدول المستخدمين المخولين للخزينة
        Schema::create('treasury_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treasury_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['treasury_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_user');
        Schema::dropIfExists('treasuries');
    }
};
