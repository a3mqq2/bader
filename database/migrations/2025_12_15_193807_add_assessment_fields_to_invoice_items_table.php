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
            $table->enum('assessment_status', ['pending', 'completed'])->default('pending')->after('total');
            $table->text('assessment_result')->nullable()->after('assessment_status');
            $table->text('assessment_notes')->nullable()->after('assessment_result');
            $table->foreignId('assessed_by')->nullable()->after('assessment_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('assessed_at')->nullable()->after('assessed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['assessed_by']);
            $table->dropColumn(['assessment_status', 'assessment_result', 'assessment_notes', 'assessed_by', 'assessed_at']);
        });
    }
};
