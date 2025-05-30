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
        // User::factory()->state([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ])->create();


        // \App\Models\depensesGroupe::factory()
        // ->count(5)
        // ->create();

        \App\Models\Member::factory()   
        ->count(5)
        ->create();

    }
}
