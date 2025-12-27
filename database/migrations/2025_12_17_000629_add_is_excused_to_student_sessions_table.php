<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->boolean('is_excused')->default(false)->after('status'); // هل الغياب بإذن؟
            $table->foreignId('excused_absence_id')->nullable()->after('is_excused')
                ->constrained('excused_absences')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->dropForeign(['excused_absence_id']);
            $table->dropColumn(['is_excused', 'excused_absence_id']);
        });
    }
};
