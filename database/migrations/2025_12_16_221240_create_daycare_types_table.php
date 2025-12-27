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
        Schema::create('daycare_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم نوع الرعاية
            $table->decimal('price', 10, 2); // السعر الشهري
            $table->boolean('is_active')->default(true); // الحالة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daycare_types');
    }
};
