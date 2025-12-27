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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الطالب
            $table->string('name'); // اسم الطالب
            $table->date('birth_date'); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female']); // الجنس

            // معلومات ولي الأمر
            $table->string('guardian_name'); // اسم ولي الأمر
            $table->string('phone'); // رقم الهاتف الأساسي
            $table->string('phone_alt')->nullable(); // رقم هاتف بديل
            $table->text('address')->nullable(); // العنوان

            $table->text('notes')->nullable(); // ملاحظات

            // حالة الطالب: new = جديد, active = نشط
            $table->enum('status', ['new', 'active'])->default('new');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
