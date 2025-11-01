<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Eloquent\Model as MongoModel;

class MongoDbQuickTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip if MongoDB not available
        if (!extension_loaded('mongodb')) {
            $this->markTestSkipped('MongoDB extension not available');
        }
        
        // Try to connect to MongoDB
        try {
            $connection = DB::connection('mongodb');
            $connection->table('test')->limit(1)->get();
        } catch (\Exception $e) {
            $this->markTestSkipped('MongoDB not available: ' . $e->getMessage());
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            \Pawsmedz\JsonFilter\UnifiedJsonFilterServiceProvider::class,
            \MongoDB\Laravel\MongoDBServiceProvider::class, 
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        
        // Configure MongoDB connection
        $app['config']->set('database.connections.mongodb', [
            'driver' => 'mongodb',
            'host' => '127.0.0.1',
            'port' => 27017,
            'database' => 'test',
        ]);
        
        $app['config']->set('database.default', 'mongodb');
    }

    public function test_mongodb_connection_works(): void
    {
        // Simple connection test
        $result = DB::connection('mongodb')->table('users')->count();
        $this->assertIsInt($result);
        
        echo "\n✅ MongoDB connection successful!\n";
    }

    public function test_mongodb_adapter_detection(): void
    {
        $builder = MongoUser::query();
        $adapter = \Pawsmedz\JsonFilter\Adapters\AdapterFactory::getAdapter($builder);
        
        echo "\n🔍 Detected adapter: " . get_class($adapter) . "\n";
        echo "📊 Database type: " . $adapter->getDatabaseType() . "\n";
        
        $this->assertNotNull($adapter);
        $this->assertInstanceOf(\Pawsmedz\JsonFilter\Adapters\DatabaseAdapter::class, $adapter);
    }

    public function test_basic_mongodb_operations(): void
    {
        // Insert test data
        DB::connection('mongodb')->table('users')->insert([
            ['name' => 'John Doe', 'meta' => ['status' => 'active', 'score' => 90]],
            ['name' => 'Jane Smith', 'meta' => ['status' => 'inactive', 'score' => 85]],
        ]);

        // Test basic query
        $users = DB::connection('mongodb')->table('users')->get();
        $this->assertGreaterThan(0, $users->count());
        
        echo "\n📋 Found " . $users->count() . " users in MongoDB\n";
        
        DB::connection('mongodb')->table('users')->truncate();
    }
}

// Simple MongoDB User model for testing
class MongoUser extends MongoModel
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $fillable = ['name', 'meta'];
}