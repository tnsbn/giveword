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
//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//            'password' => '123123',
//        ]);
        User::factory()->create([
            'name' => 'Johny Depp',
            'email' => 'jd@suw.com',
            'password' => '123123',
        ]);
        User::factory()->create([
            'name' => 'Daniel',
            'email' => 'daniel@suw.com',
            'password' => '123123',
        ]);
    }
}
