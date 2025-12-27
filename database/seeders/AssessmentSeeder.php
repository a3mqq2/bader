<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        Assessment::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'دراسة الحالة',
                'price' => 100.00,
                'is_active' => true,
                'description' => 'دراسة الحالة الأساسية للطالب',
                'created_by' => 1,
            ]
        );
    }
}
