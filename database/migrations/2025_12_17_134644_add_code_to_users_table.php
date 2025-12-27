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
        Schema::table('users', function (Blueprint $table) {
            $table->string('code', 6)->unique()->nullable()->after('id');
        });

        // توليد أكواد للمستخدمين الحاليين
        $users = \App\Models\User::whereNull('code')->get();
        foreach ($users as $user) {
            $user->code = \App\Models\User::generateCode();
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
