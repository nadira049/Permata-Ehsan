<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'nadirah',
            'full_name' => 'nadirah binti rafi',
            'address' => 'jalan 1, shah alam',
            'email' => 'nadirah@gmail.com',
            'password' => bcrypt('nadirah123'),
            'role' => 'admin',
        ]);
    }
}
