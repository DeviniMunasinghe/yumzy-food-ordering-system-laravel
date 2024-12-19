<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'testadmin1@gmail.com',
            'username' => 'testadmin1',
            'password' => bcrypt('testadmin1'),
            'role' => 'admin',
        ]); 

        User::create([
            'email' => 'superadmin@example.com',
            'username' => 'superadmin',
            'password' => bcrypt('superadmin'),
            'role' => 'super_admin',
        ]);
    }
}
