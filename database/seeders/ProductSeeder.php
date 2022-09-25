<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
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

        $faker = Faker::create();
        foreach (range(1, 1000) as $iNumber) {
            DB::table('product')->insert([
                [
                    'category_no' => rand(1,12),
                    'name'        => 'Product ' . $iNumber,
                    'description' => $faker->words(5, true),
                    'price'       => $faker->randomFloat(2, 100, 1)
                ]
            ]);
        }
    }
}
