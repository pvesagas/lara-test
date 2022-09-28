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
        foreach (range(1,12) as $iNumber) {
            DB::table('category')->insert([
                [
                    'category' => 'Category ' . $iNumber
                ]
            ]);
        }
    }
}
