<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CounterSeeder extends Seeder
{
    public function run()
    {
        DB::table('counters')->insert([
            'id' => 'sample'
        ]);
    }
}
