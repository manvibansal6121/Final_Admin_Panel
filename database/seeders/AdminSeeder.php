<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'name' => 'Manvi',
            'email' => 'manvi50716@gmail.com',
            'password' => Hash::make('123456'),
            'phone_number'=>'7973063457',
            'profile'=>'1',
        ]);
    }
}
