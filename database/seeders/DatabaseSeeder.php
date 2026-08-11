<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Toxirjon Satimov',
            'email' => 'admin@tezsavdo.uz',
            'password' => Hash::make('parol123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Kamola Sotuvchi',
            'email' => 'kassir@tezsavdo.uz',
            'password' => Hash::make('parol123'),
            'role' => 'sotuvchi',
        ]);

        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
