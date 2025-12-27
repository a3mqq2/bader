<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daycare_subscriptions', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('price')->constrained('invoices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('daycare_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
};
