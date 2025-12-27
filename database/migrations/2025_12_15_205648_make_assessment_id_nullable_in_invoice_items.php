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
        Schema::table('invoice_items', function (Blueprint $table) {
            // حذف الـ foreign key القديم
            $table->dropForeign(['assessment_id']);
            // جعل العمود nullable
            $table->unsignedBigInteger('assessment_id')->nullable()->change();
            // إعادة إضافة الـ foreign key مع set null
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->unsignedBigInteger('assessment_id')->nullable(false)->change();
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
        });
    }
};
