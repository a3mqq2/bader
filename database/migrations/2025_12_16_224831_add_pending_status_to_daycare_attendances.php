<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // تغيير enum لإضافة pending
        DB::statement("ALTER TABLE daycare_attendances MODIFY COLUMN status ENUM('pending', 'present', 'absent') DEFAULT 'pending'");

        // تحديث السجلات الموجودة إلى pending
        DB::table('daycare_attendances')->where('status', 'absent')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE daycare_attendances MODIFY COLUMN status ENUM('present', 'absent') DEFAULT 'absent'");
    }
};
