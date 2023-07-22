<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import the DB facade

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'permission' => 'Dashboard',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Users',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Products',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Content Page',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'FAQ',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Contact',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Staff',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
             [
                'permission' => 'Profile',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
             [
                'permission' => 'Notification',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'permission' => 'Backup',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
        ];

        DB::table('permissions')->insert($data);
    }
}
