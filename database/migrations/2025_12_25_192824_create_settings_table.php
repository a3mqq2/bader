<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, decimal, boolean, json, time
            $table->string('group')->default('general'); // general, work_hours, incentives, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        DB::table('settings')->insert([
            // إعدادات الدوام
            [
                'key' => 'work_start_time',
                'value' => '08:30',
                'type' => 'time',
                'group' => 'work_hours',
                'label' => 'بداية الدوام',
                'description' => 'وقت بداية الدوام الرسمي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_end_time',
                'value' => '12:30',
                'type' => 'time',
                'group' => 'work_hours',
                'label' => 'نهاية الدوام',
                'description' => 'وقت نهاية الدوام الرسمي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // إعدادات الحوافز
            [
                'key' => 'session_incentive_amount',
                'value' => '10.00',
                'type' => 'decimal',
                'group' => 'incentives',
                'label' => 'حافز الجلسة',
                'description' => 'المبلغ الذي يضاف لرصيد الموظف عند إكمال جلسة علاجية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'daycare_incentive_amount',
                'value' => '5.00',
                'type' => 'decimal',
                'group' => 'incentives',
                'label' => 'حافز الرعاية النهارية',
                'description' => 'المبلغ الذي يضاف لرصيد الموظف عند تسجيل حضور رعاية نهارية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'incentives_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'incentives',
                'label' => 'تفعيل نظام الحوافز',
                'description' => 'تفعيل أو إيقاف نظام الحوافز التلقائية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
