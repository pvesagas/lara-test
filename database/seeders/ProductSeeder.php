<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insert([
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 1',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 2',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 3',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 4',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 5',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 6',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 7',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 8',
                'description' => 'This is a description',
                'price'       => 10.15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 9',
                'description' => 'This is a description',
                'price'       => 100
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 10',
                'description' => 'This is a description',
                'price'       => 15
            ],
            [
                'category_no' => rand(1,4),
                'name'        => 'Product 11',
                'description' => 'This is a description',
                'price'       => 10
            ]
        ]);
    }
}
