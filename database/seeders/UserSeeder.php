<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::factory()->count(50)->hasPosts(1)->create();
        DB::table('users')->insert([
            'name' => 'ChenRay',
            'email' => 'chenraygogo@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
