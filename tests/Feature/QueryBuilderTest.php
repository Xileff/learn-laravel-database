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
        DB::delete('DELETE FROM counters');
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
    public function helperInsertCategories()
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
    public function helpercheckCountAndLog($expectedAmount, $result)
    {
        $this->assertCount($expectedAmount, $result);

        $result->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // SELECT * FROM categories WHERE name = ? OR name = ?
    public function testWhereOrBiasa()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')
            ->where('name', '=', 'Gadget')
            ->orWhere('name', '=', 'Food')
            ->get();

        $this->helpercheckCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE (name = ? OR name = ?)
    public function testWhereOrPakeKurung()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')->where(function (Builder $builder) {
            $builder->where('name', '=', 'Gadget');
            $builder->orWhere('name', '=', 'Food');
        })->get();

        $this->helpercheckCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE name = ? AND name = ?
    public function testWhereAndBiasa()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')
            ->where('id', '=', 'GADGET')
            ->where('name', '=', 'Gadget')
            ->get();

        $this->helpercheckCountAndLog(1, $result);
    }

    // SELECT * FROM categories WHERE (name = ? AND name = ?)
    public function testWhereAndPakeKurung()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')->where(function (Builder $builder) {
            $builder
                ->where('id', '=', 'GADGET')
                ->where('name', '=', 'Gadget');
        })->get();

        $this->helpercheckCountAndLog(1, $result);
    }

    // SELECT * from categories WHERE created_at between ? and ?
    public function testWhereBetween()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')->whereBetween('created_at', ['2020-09-01 00:00:00', '2020-11-01 00:00:00'])->get();

        $this->helpercheckCountAndLog(5, $result);
    }

    // SELECT * FROM categories WHERE id IN (?, ?) 
    public function testWhereIn()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')->whereIn('id', ['SMARTPHONE', 'LAPTOP'])->get();

        $this->helpercheckCountAndLog(2, $result);
    }

    // SELECT * FROM categories WHERE description IS NULL
    public function testWhereNull()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')->whereNull('description')->get();

        $this->helpercheckCountAndLog(5, $result);
    }

    // SELECT * FROM categories WHERE DATE(created_at) = ?
    public function testWhereDate()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')
            ->whereDate('created_at', '=', '2020-10-10')
            ->get();

        $this->helpercheckCountAndLog(5, $result);
    }

    // UPDATE categories SET name = ? WHERE id = ?  
    public function testUpdate()
    {
        $this->helperInsertCategories();

        DB::table('categories')->where('id', '=', 'SMARTPHONE')->update([
            'name' => 'Handphone'
        ]);

        $result = DB::table('categories')->where('name', '=', 'Handphone')->get();

        $this->helpercheckCountAndLog(1, $result);
    }

    // select exists(select * from `categories` where (`id` = ?)) as `exists`  
    // insert into `categories` (`id`, `name`, `description`, `created_at`) values (?, ?, ?, ?)  
    public function testUpsert()
    {
        DB::table('categories')->updateOrInsert([
            'id' => 'VOUCHER', // where nya
        ], [
            'name' => 'Voucher',
            'description' => 'Ticket and Voucher',
            'created_at' => '2020-10-10 00:00:00'
        ]);

        $result = DB::table('categories')->where('id', '=', 'VOUCHER')->get();

        $this->helpercheckCountAndLog(1, $result);
    }

    public function helperCreateCounter()
    {
        DB::table('counters')->insert([
            'id' => 'sample'
        ]);
    }

    public function testIncrement()
    {
        $this->helperCreateCounter();
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 1);

        $result = DB::table('counters')->where('id', '=', 'sample')->get();
        $this->helpercheckCountAndLog(1, $result);
    }

    // delete from `categories` where `id` = ? 
    public function testDelete()
    {
        $this->helperInsertCategories();

        DB::table('categories')->where('id', '=', 'SMARTPHONE')->delete();
        // DB::table('categories')->delete('SMARTPHONE'); // must id

        $result = DB::table('categories')->where('id', '=', 'SMARTPHONE')->get();
        $this->helpercheckCountAndLog(0, $result);
    }
}
