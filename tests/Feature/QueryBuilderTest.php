<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPSTORM_META\map;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM products');
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

    public function helperCreateCounter()
    {
        DB::table('counters')->insert([
            'id' => 'sample'
        ]);
    }

    public function helperInsertProducts()
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

    // SELECT EXISTS(SELECT * FROM `categories` WHERE (`id` = ?)) AS `exists`  
    // INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES (?, ?, ?, ?)  
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

    public function testIncrement()
    {
        $this->helperCreateCounter();
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 1);

        $result = DB::table('counters')->where('id', '=', 'sample')->get();
        $this->helpercheckCountAndLog(1, $result);
    }

    // DELETE FROM `categories` WHERE `id` = ? 
    public function testDelete()
    {
        $this->helperInsertCategories();

        DB::table('categories')->where('id', '=', 'SMARTPHONE')->delete();
        // DB::table('categories')->delete('SMARTPHONE'); // must id

        $result = DB::table('categories')->where('id', '=', 'SMARTPHONE')->get();
        $this->helpercheckCountAndLog(0, $result);
    }

    /*
    SELECT 
        `products`.`id`, `products`.`name`, `categories`.`name` as `category_name`, `products`.`price` 
    FROM `products` INNER JOIN `categories` 
    ON `products`.`category_id` = `categories`.`id`
    */
    public function testJoin()
    {
        $this->helperInsertCategories();
        $this->helperInsertProducts();

        $result = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.name', 'categories.name as category_name', 'products.price')
            ->get();

        $this->helpercheckCountAndLog(2, $result);
    }

    // SELECT * FROM `products` WHERE `id` IS NOT NULL ORDER BY `price` ASC, `name` DESC
    public function testOrderBy()
    {
        $this->helperInsertCategories();
        $this->helperInsertProducts();

        $result = DB::table('products')
            ->whereNotNull('id')
            ->orderBy('price', 'asc')
            ->orderBy('name', 'desc')
            ->get();

        $this->helpercheckCountAndLog(2, $result);
        $this->assertEquals(2, $result[0]->id); // Samsung 18jt
        $this->assertEquals(1, $result[1]->id); // iPhone 20jt
    }

    // SELECT * FROM `categories` LIMIT 2 OFFSET 2
    public function testPaging()
    {
        $this->helperInsertCategories();

        $result = DB::table('categories')
            ->skip(0)
            ->take(2)
            ->get();

        $this->helpercheckCountAndLog(2, $result);
    }

    public function helperInsertManyCategories()
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table('categories')->insert([
                'id' => "CATEGORY-$i",
                'name' => "Category $i",
                "created_at" => "2020-10-10 00:00:00"
            ]);
        }
    }

    // SELECT * FROM `categories` ORDER BY `id` ASC LIMIT 10 OFFSET 70
    // mirip paging, tapi ini gunanya utk query banyak
    public function testChunk()
    {
        $this->helperInsertManyCategories(); // 100
        DB::table('categories')->orderBy('id')
            ->chunk(10, function ($categories) {
                Log::info("Start Chunk");
                $this->helpercheckCountAndLog(10, $categories);
                Log::info("End Chunk");
            });
    }

    // Mirip chunking tapi hasilnya lazy collection
    public function testLazy()
    {
        $this->helperInsertManyCategories();

        $collection = DB::table('categories')->orderBy('id')->lazy(10);
        $collection->each(function ($item) {
            // saat $item dibutuhkan, barulah lazy $collection ngequery 10 data
            Log::info(json_encode($item));
        });

        $this->assertNotNull($collection);
    }

    // Sudah lazy, cuma diambil 3 pula, jadi sisa 7 + 90 data belum diquery lagi
    // take() nya punya LazyCollection, bukan query builder
    public function testLazyWithTake()
    {
        $this->helperInsertManyCategories();
        $collection = DB::table('categories')->orderBy('id')->lazy(10)->take(3);

        $this->assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
}
