<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import the DB facade

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'category' => 'Category 1',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 2',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 3',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 4',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 5',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 6',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 7',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 8',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 9',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
            [
                'category' => 'Category 10',
                'created_at' => '2023-07-12 11:02:12',
                'updated_at' => '2023-07-12 11:02:12',
            ],
        ];

        DB::table('categories')->insert($data);
    }
}
