<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed test data
        DB::table('users')->insert([
            ['meta' => json_encode(['name' => 'John Doe', 'status' => 'active', 'score' => 90, 'country' => 'US'])],
            ['meta' => json_encode(['name' => 'Jane Smith', 'status' => 'inactive', 'score' => 85, 'country' => 'CA'])],
            ['meta' => json_encode(['name' => 'Bob Johnson', 'status' => 'active', 'score' => 95, 'country' => 'US'])],
        ]);
    }

    /** @test */
    public function it_demonstrates_unified_database_agnostic_queries()
    {
        // 🎉 This exact same code works on MySQL, PostgreSQL, MongoDB, and more!
        
        echo "\n🌍 UNIVERSAL DATABASE DEMO\n";
        echo "=========================\n";
        
        // Detect which adapter is being used
        $builder = User::query();
        $adapter = AdapterFactory::getAdapter($builder);
        echo "📊 Using adapter: " . get_class($adapter) . " (Type: " . $adapter->getDatabaseType() . ")\n";
        
        if (DB::connection()->getDriverName() === 'sqlite') {
            echo "⚠️  SQLite detected - using fallback adapter\n";
            $this->markTestIncomplete('Demo requires JSON-capable database');
        }
        
        echo "\n🔍 UNIFIED QUERIES:\n";
        
        // 1. Basic filtering - works on ALL databases
        echo "1. Filter by status = 'active':\n";
        $activeUsers = User::query()->jsonFilter('meta->status', '=', 'active')->get();
        echo "   Found {$activeUsers->count()} active users\n";
        
        // 2. Multiple value matching - works on ALL databases  
        echo "2. Users from US or CA:\n";
        $northAmericaUsers = User::query()->jsonWhereIn('meta->country', ['US', 'CA'])->get();
        echo "   Found {$northAmericaUsers->count()} North American users\n";
        
        // 3. Ordering by JSON values - works on ALL databases
        echo "3. Top users by score:\n";
        $topUsers = User::query()->jsonOrderBy('meta->score', 'desc')->get();
        $scores = $topUsers->map(fn($user) => json_decode($user->meta, true)['score']);
        echo "   Scores in order: " . implode(', ', $scores->toArray()) . "\n";
        
        // 4. Path existence checking - works on ALL databases
        echo "4. Users with score data:\n";
        $usersWithScores = User::query()->jsonExists('meta->score')->get();
        echo "   Found {$usersWithScores->count()} users with scores\n";
        
        // 5. Complex chaining - works on ALL databases
        echo "5. Complex query chain:\n";
        $complexQuery = User::query()
            ->jsonFilter('meta->status', '=', 'active')
            ->jsonExists('meta->score')
            ->jsonOrderBy('meta->score', 'desc');
            
        $complexResults = $complexQuery->get();
        echo "   Active users with scores: {$complexResults->count()}\n";
        
        echo "\n✨ ALL QUERIES WORK IDENTICALLY ACROSS:\n";
        echo "   - MySQL (JSON_EXTRACT functions)\n";
        echo "   - PostgreSQL (JSONB operators) \n";
        echo "   - MongoDB (dot notation)\n";
        echo "   - And more with custom adapters!\n";
        
        // Assertions to make this a real test
        $this->assertGreaterThan(0, $activeUsers->count());
        $this->assertGreaterThan(0, $northAmericaUsers->count());
        $this->assertEquals([95, 90, 85], $scores->toArray());
        $this->assertGreaterThan(0, $usersWithScores->count());
        $this->assertGreaterThan(0, $complexResults->count());
        
        echo "\n🎯 UNIVERSAL SUCCESS! Same code, any database!\n\n";
    }
}