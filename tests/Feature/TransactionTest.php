<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function () {
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
            ]);
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                'FOOD', 'Food', 'Food Category', '2020-10-10 10:10:10'
            ]);
        });

        $result = DB::select("SELECT * FROM categories");
        $this->assertCount(2, $result);
    }

    public function testTransactionFailed()
    {
        try {
            DB::transaction(function () {
                DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                    'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
                ]);
                DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                    'GADGET', 'Food', 'Food Category', '2020-10-10 10:10:10'
                ]);
            });
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        } finally {
            $result = DB::select("SELECT * FROM categories");
            $this->assertCount(0, $result);
        }
    }

    public function testTransactionFailedManual()
    {
        try {
            DB::beginTransaction();
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
            ]);
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                'GADGET', 'Food', 'Food Category', '2020-10-10 10:10:10'
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        } finally {
            $result = DB::select("SELECT * FROM categories");
            $this->assertCount(0, $result);
        }
    }
}
