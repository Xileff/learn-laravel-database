<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            'id' => '1',
            'name' => 'iPhone 14 Pro Max',
            'category_id' => 'SMARTPHONE',
            'price' => 20000000
        ]);

        DB::table('products')->insert([
            'id' => '2',
            'name' => 'Samsung Galaxy S21 Ultra',
            'category_id' => 'SMARTPHONE',
            'price' => 18000000
        ]);
    }
}
