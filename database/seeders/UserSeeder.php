<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 10 users using the factory
        User::factory(10)->create();

        // Optionally, create the default user manually
        User::create([
            'name' => 'Admin User',
            'email' => 'singh.sudhakar@tftus.com',
            'password' => bcrypt('Pass@123'),
        ]);
    }
}
