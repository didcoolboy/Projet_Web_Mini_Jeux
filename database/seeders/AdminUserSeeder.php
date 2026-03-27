<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@pixelzone.io',
            'password' => bcrypt('mot-de-passe-solide'),
            'role'     => 'admin',
        ]);
    }
}
