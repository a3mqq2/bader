<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'مدير النظام',
            'phone' => '000000',
            'password' => Hash::make('000000'),
            'is_active' => true,
        ]);

        $this->call([
            PermissionSeeder::class,
            AssessmentSeeder::class,
        ]);
    }
}
