<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@example.com';
        $user->phone = '0755237692';
        $user->status = 'active';
        $user->password = Hash::make('password');
        $user->save();

        // Assign all permissions to this user
        $user->syncPermissions(Permission::all());
    }
}
