<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed test data
        DB::table('users')->insert([
            ['meta' => json_encode(['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'active', 'score' => 90, 'skills' => ['php', 'laravel']])],
            ['meta' => json_encode(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'inactive', 'score' => 85, 'skills' => ['vue', 'javascript']])],
            ['meta' => json_encode(['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'status' => 'active', 'score' => 95, 'skills' => ['php', 'mysql']])],
        ]);
    }

    /** @test */
    public function it_detects_the_correct_adapter_for_current_connection()
    {
        $builder = User::query();
        $adapter = AdapterFactory::getAdapter($builder);
        
        $this->assertNotNull($adapter);
        $this->assertInstanceOf(\Pawsmedz\JsonFilter\Adapters\DatabaseAdapter::class, $adapter);
        
        // Should return an adapter type
        $this->assertIsString($adapter->getDatabaseType());
    }

    /** @test */
    public function it_uses_unified_json_filter_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        // Test unified jsonFilter - should work regardless of database type
        $results = User::query()
            ->jsonFilter('meta->status', '=', 'active')
            ->get();

        $this->assertCount(2, $results);
        
        foreach ($results as $user) {
            $meta = json_decode($user->meta, true);
            $this->assertEquals('active', $meta['status']);
        }
    }

    /** @test */
    public function it_uses_unified_json_where_in_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonWhereIn('meta->status', ['active', 'inactive'])
            ->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_uses_unified_json_contains_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonContains('meta->skills', 'php')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
        
        foreach ($results as $user) {
            $meta = json_decode($user->meta, true);
            $this->assertContains('php', $meta['skills']);
        }
    }

    /** @test */
    public function it_uses_unified_json_exists_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonExists('meta->skills')
            ->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_uses_unified_json_order_by_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonOrderBy('meta->score', 'desc')
            ->get();

        $this->assertCount(3, $results);
        
        $scores = $results->map(function ($user) {
            return json_decode($user->meta, true)['score'];
        });
        
        // Check that scores are in descending order
        $this->assertEquals([95, 90, 85], $scores->toArray());
    }

    /** @test */
    public function it_uses_unified_json_select_across_database_types()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonSelect('meta->name as user_name')
            ->jsonSelect('meta->status as user_status')
            ->get();

        $this->assertCount(3, $results);
        
        foreach ($results as $user) {
            // Check that the selected JSON fields are accessible via Eloquent's magic methods
            $this->assertNotNull($user->user_name, 'user_name should be accessible');
            $this->assertNotNull($user->user_status, 'user_status should be accessible');
            $this->assertNotEmpty($user->user_name);
            $this->assertNotEmpty($user->user_status);
        }
    }

    /** @test */
    public function it_chains_multiple_unified_operations()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        $results = User::query()
            ->jsonFilter('meta->status', '=', 'active')
            ->jsonExists('meta->skills')
            ->jsonContains('meta->skills', 'php')
            ->jsonSelect('meta->name as name')
            ->jsonSelect('meta->score as score')
            ->jsonOrderBy('meta->score', 'desc')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
        
        foreach ($results as $user) {
            $this->assertNotNull($user->name, 'name should be accessible');
            $this->assertNotNull($user->score, 'score should be accessible');
        }
    }

    /** @test */
    public function it_maintains_backward_compatibility_with_legacy_macros()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite uses fallback adapter.');
        }

        // Legacy macros should still work (if they exist)
        $builder = User::query();
        
        // Check if legacy macros are registered
        if (method_exists($builder, 'hasMacro') && $builder->hasMacro('jsonFilterLegacy')) {
            $results = $builder->jsonFilterLegacy('meta->status', '=', 'active')->get();
            $this->assertCount(2, $results);
        } else {
            $this->markTestSkipped('Legacy macros not registered in this configuration.');
        }
    }
}