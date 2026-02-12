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
        User::factory()->create([
            'name' => 'Tiago',
            'email' => 'tiago@tiremoto.com.br',
            'password' => 'Seguro@2020',
        ]);

        $this->call(AnalyticsSeeder::class);
    }
}
