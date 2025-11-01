<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;

class SelectAllTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed test data
        DB::table('users')->insert([
            ['meta' => json_encode(['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'active', 'score' => 90])],
            ['meta' => json_encode(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'inactive', 'score' => 85])],
        ]);
    }

    /** @test */
    public function it_maintains_select_all_functionality_when_using_pluck()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not support JSON queries.');
        }

        // This should work without issues because ensureSelectAll() is called
        $ids = User::query()
            ->jsonFilter('meta->status', '=', 'active')
            ->pluck('id')
            ->toArray();

        $this->assertCount(1, $ids);
        $this->assertEquals([1], $ids);
    }

    /** @test */
    public function it_maintains_select_all_functionality_when_chaining_multiple_macros()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not support JSON queries.');
        }

        // Chain multiple macros and then use get() - should return all columns
        $users = User::query()
            ->jsonFilter('meta->status', '=', 'active')
            ->jsonOrderBy('meta->score', 'desc')
            ->jsonExists('meta->score')
            ->get();

        $this->assertCount(1, $users);
        
        $user = $users->first();
        $this->assertEquals(1, $user->id);
        $this->assertNotNull($user->meta);
        
        $meta = json_decode($user->meta, true);
        $this->assertEquals('John Doe', $meta['name']);
        $this->assertEquals('active', $meta['status']);
    }

    /** @test */
    public function it_works_with_explicit_select_statements()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not support JSON queries.');
        }

        // When explicit select is used, ensureSelectAll should not interfere
        $users = User::query()
            ->select('id')
            ->jsonFilter('meta->status', '=', 'active')
            ->get();

        $this->assertCount(1, $users);
        
        $user = $users->first();
        $this->assertEquals(1, $user->id);
        $this->assertNull($user->meta); // meta should not be selected
    }
}