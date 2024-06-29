<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFoodSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            'id' => '3',
            'name' => 'Bakso',
            'category_id' => 'FOOD',
            'price' => 20000
        ]);

        DB::table('products')->insert([
            'id' => '4',
            'name' => 'Mi Ayam',
            'category_id' => 'FOOD',
            'price' => 20000
        ]);
    }
}
