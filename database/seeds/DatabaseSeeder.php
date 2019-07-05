<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            'username' => 'arya.bawanta',
            'email' => 'arya.bawanta@gmail.com',
            'name' => 'Arya Bawanta',
            'password' => Hash::make('password')
        ]);
    }
}
