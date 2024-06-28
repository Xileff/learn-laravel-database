<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }

    public function testCrud(): void
    {
        DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
            'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
        ]);

        $results = DB::select("SELECT * FROM categories WHERE id = ?", ['GADGET']);

        $this->assertCount(1, $results);
        $this->assertEquals('GADGET', $results[0]->id);
        $this->assertEquals('Gadget', $results[0]->name);
        $this->assertEquals('Gadget Category', $results[0]->description);
        $this->assertEquals('2020-10-10 10:10:10', $results[0]->created_at);
    }

    public function testNamedBinding(): void
    {
        DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
            'id' => 'GADGET',
            'name' => 'Gadget',
            'description' => 'Gadget Category',
            'created_at' => '2020-10-10 10:10:10'
        ]);

        $results = DB::select("SELECT * FROM categories WHERE id = :id", ['id' => 'GADGET']);

        $this->assertCount(1, $results);
        $this->assertEquals('GADGET', $results[0]->id);
        $this->assertEquals('Gadget', $results[0]->name);
        $this->assertEquals('Gadget Category', $results[0]->description);
        $this->assertEquals('2020-10-10 10:10:10', $results[0]->created_at);
    }
}
