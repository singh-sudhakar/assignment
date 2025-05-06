<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 10 users using the factory
        User::factory(1)->create();

        // Optionally, create the default user manually
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'sudhakar@tftus.com',
            'password' => bcrypt('Pass@123'),
        ]);

        // Generate Sanctum token and echo it
        if ($user->tokens()->count() === 0) {
            $token = $user->createToken('TestToken')->plainTextToken;
            echo "\nSanctum Token for sudhakar@tftus.com:\n\n";
            echo "Bearer {$token}\n\n";
            // Store the token in a text file
            File::put(storage_path('app/public/sanctum_token.txt'), $token);

            echo "Sanctum Token for sudhakar@tftus.com has been stored in 'storage/app/public/sanctum_token.txt'.\n";

        }
    }
}
