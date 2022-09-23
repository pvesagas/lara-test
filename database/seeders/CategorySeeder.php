<?php

namespace Database\Seeders;

use App\Models\ProductCategoryModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category')->insert([
            [
                'category' => 'Category 1'
            ],
            [
                'category' => 'Category 2'
            ],
            [
                'category' => 'Category 3'
            ],
            [
                'category' => 'Category 4'
            ],
        ]);
    }
}
