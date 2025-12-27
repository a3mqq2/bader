<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
       $roles = [
            'admin' => 'مدير النظام',
            'specialist' => 'أخصائي',
            'supervisor' => 'مشرف',
            'accountant' => 'محاسب',
       ];

       foreach( $roles as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
       }

       $user = User::find(1);
       if ($user) {
            $user->assignRole('admin');
       }
    }
}
