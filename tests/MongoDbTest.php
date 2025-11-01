<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;

class MongoDbTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip if MongoDB extension not available
        if (!extension_loaded('mongodb')) {
            $this->markTestSkipped('MongoDB extension not available');
        }
        
        // Skip if MongoDB packages not available
        if (!class_exists('\MongoDB\Laravel\Eloquent\Model') && 
            !class_exists('\Jenssegers\Mongodb\Eloquent\Model')) {
            $this->markTestSkipped('MongoDB Laravel package not available');
        }
        
        // Try to connect to MongoDB - skip if not available
        try {
            // This will fail gracefully if MongoDB is not configured or running
            $connection = DB::connection('mongodb');
            $connection->table('test')->limit(1)->get();
        } catch (\Exception $e) {
            $this->markTestSkipped('MongoDB not available or not configured: ' . $e->getMessage());
        }
    }

    protected function getPackageProviders($app)
    {
        $providers = [
            \Pawsmedz\JsonFilter\UnifiedJsonFilterServiceProvider::class,
        ];
        
        // Add MongoDB service provider if available
        if (class_exists('\MongoDB\Laravel\MongoDBServiceProvider')) {
            $providers[] = \MongoDB\Laravel\MongoDBServiceProvider::class;
        } elseif (class_exists('\Jenssegers\Mongodb\MongodbServiceProvider')) {
            // Note: Jenssegers has different casing for MongodbServiceProvider
            $providers[] = \Jenssegers\Mongodb\MongodbServiceProvider::class;
        }
        
        return $providers;
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
        // Use query builder directly
        $builder = DB::connection('mongodb')->table('users');
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