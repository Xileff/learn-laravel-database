<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');
    }

    public function testInsert()
    {
        DB::table('categories')->insert([
            'id' => 'GADGET',
            'name' => 'Gadget'
        ]);

        DB::table('categories')->insert([
            'id' => 'FOOD',
            'name' => 'Food'
        ]);

        $result = DB::select('SELECT COUNT(id) AS total FROM categories');
        $this->assertEquals(2, $result[0]->total);
    }

    public function testSelect()
    {
        $this->testInsert();

        $result = DB::table('categories')->select(['id', 'name'])->get();
        $this->assertNotNull($result);
        $this->assertCount(2, $result->all());

        $result->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // helper function for creating 4 dummy data
    public function insertCategories()
    {
        DB::table('categories')->insert([
            'id' => 'GADGET',
            'name' => 'Gadget',
            'created_at' => '2020-10-10 10:10:10'
        ]);

        DB::table('categories')->insert([
            'id' => 'FOOD',
            'name' => 'Food',
            'created_at' => '2020-10-10 10:10:10'
        ]);

        DB::table('categories')->insert([
            'id' => 'LAPTOP',
            'name' => 'Laptop',
            'created_at' => '2020-10-10 10:10:10'
        ]);

        DB::table('categories')->insert([
            'id' => 'FASHION',
            'name' => 'Fashion',
            'created_at' => '2020-10-10 10:10:10'
        ]);

        DB::table('categories')->insert([
            'id' => 'SMARTPHONE',
            'name' => 'Smartphone',
            'created_at' => '2020-10-10 10:10:10'
        ]);
    }

    // helper function for asserting amount and logging
    public function checkCountAndLog($expectedAmount, $result)
    {
        $this->assertCount($expectedAmount, $result);

        $result->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // SELECT * FROM categories WHERE name = ? OR name = ?
    public function testWhereOrBiasa()
    {
        $this->insertCategories();

        $result = DB::table('categories')
            ->where('name', '=', 'Gadget')
            ->orWhere('name', '=', 'Food')
            ->get();

        $this->checkCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE (name = ? OR name = ?)
    public function testWhereOrPakeKurung()
    {
        $this->insertCategories();

        $result = DB::table('categories')->where(function (Builder $builder) {
            $builder->where('name', '=', 'Gadget');
            $builder->orWhere('name', '=', 'Food');
        })->get();

        $this->checkCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE name = ? AND name = ?
    public function testWhereAndBiasa()
    {
        $this->insertCategories();

        $result = DB::table('categories')
            ->where('id', '=', 'GADGET')
            ->where('name', '=', 'Gadget')
            ->get();

        $this->checkCountAndLog(1, $result);
    }

    // SELECT * FROM categories WHERE (name = ? AND name = ?)
    public function testWhereAndPakeKurung()
    {
        $this->insertCategories();

        $result = DB::table('categories')->where(function (Builder $builder) {
            $builder
                ->where('id', '=', 'GADGET')
                ->where('name', '=', 'Gadget');
        })->get();

        $this->checkCountAndLog(1, $result);
    }

    // SELECT * from categories WHERE created_at between ? and ?
    public function testWhereBetween()
    {
        $this->insertCategories();

        $result = DB::table('categories')->whereBetween('created_at', ['2020-09-01 00:00:00', '2020-11-01 00:00:00'])->get();

        $this->checkCountAndLog(5, $result);
    }

    // SELECT * FROM categories WHERE id IN (?, ?) 
    public function testWhereIn()
    {
        $this->insertCategories();

        $result = DB::table('categories')->whereIn('id', ['SMARTPHONE', 'LAPTOP'])->get();

        $this->checkCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE description IS NULL
    public function testWhereNull()
    {
        $this->insertCategories();

        $result = DB::table('categories')->whereNull('description')->get();

        $this->checkCountAndLog(5, $result);
    }

    // SELECT * FROM categories WHERE DATE(created_at) = ?
    public function testWhereDate()
    {
        $this->insertCategories();

        $result = DB::table('categories')
            ->whereDate('created_at', '=', '2020-10-10')
            ->get();

        $this->checkCountAndLog(5, $result);
    }
}
