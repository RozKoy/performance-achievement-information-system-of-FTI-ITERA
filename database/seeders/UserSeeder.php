<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'superadmin@superadmin.com',
            'password' => 'superadmin1',
            'name' => 'Super Admin',
            'role' => 'super admin',
            'access' => 'editor',
        ]);
    }
}
