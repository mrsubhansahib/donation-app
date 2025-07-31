<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        User::create([
            'first_name' => 'App',
            'last_name' => 'Admin',
            'title' => 'Mr',
            'role' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'city' => '',
            'country' => '',
            'address' => '',
            'zip_code' => '',
            'stripe_id' => '',
        ]);
    }
}
