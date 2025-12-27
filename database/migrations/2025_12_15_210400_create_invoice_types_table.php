<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إنشاء جدول أنواع الفواتير
        Schema::create('invoice_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إضافة أنواع افتراضية
        DB::table('invoice_types')->insert([
            ['name' => 'تقييم', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // تغيير عمود type في جدول invoices من enum إلى foreign key
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('invoice_type_id')->nullable()->after('student_id')->constrained('invoice_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_type_id']);
            $table->dropColumn('invoice_type_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('type', ['assessment', 'monthly', 'registration', 'other'])->default('assessment')->after('student_id');
        });

        Schema::dropIfExists('invoice_types');
    }
};
