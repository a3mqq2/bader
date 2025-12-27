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
        // تغيير enum الحالة لإضافة حالة "تحت التقييم"
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('new', 'under_assessment', 'active') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('new', 'active') DEFAULT 'new'");
    }
};
