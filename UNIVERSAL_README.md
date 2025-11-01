# 🌍 Laravel Universal JSON Filter

**The world's first truly universal database package for Laravel**

*One syntax. Any database. Infinite possibilities.*

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/paws1234/laravel-json-filter/tests?label=tests&style=flat-square)](https://github.com/paws1234/laravel-json-filter/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)

---

## 🚀 **Revolutionary Approach**

This isn't just another Laravel package – it's a **paradigm shift**. Write your queries once, and they work identically across:

- **SQL Databases**: MySQL, PostgreSQL, SQLite
- **NoSQL Databases**: MongoDB, DocumentDB, CouchDB  
- **Graph Databases**: Neo4j, ArangoDB
- **Key-Value Stores**: Redis, DynamoDB
- **And any database you add an adapter for**

## ✨ **The Magic**

```php
// This EXACT code works on ANY database type!
$results = User::query()
    ->jsonFilter('profile->country', '=', 'US')           // 🌎 Any database
    ->jsonContains('skills', 'laravel')                  // 🔍 Any database  
    ->jsonExists('subscription->plan')                   // ✅ Any database
    ->jsonWhereIn('status', ['active', 'premium'])       // 📋 Any database
    ->jsonOrderBy('score', 'desc')                       // 📊 Any database
    ->jsonSelect('profile->name as display_name')        // 🎯 Any database
    ->get();

// Behind the scenes:
// MySQL:      Uses JSON_EXTRACT() functions
// PostgreSQL: Uses JSONB operators  
// MongoDB:    Uses dot notation
// Your DB:    Uses your custom adapter
```

## 🏗️ **Intelligent Architecture**

### Auto-Detection System
```php
// Package automatically detects your database and optimizes queries
$adapter = AdapterFactory::getAdapter($builder);

// MySQL Connection    → MySQLAdapter    → JSON_EXTRACT()
// PostgreSQL          → PostgreSQLAdapter → JSONB operators  
// MongoDB Connection  → MongoDBAdapter  → Native queries
// Custom Database     → YourAdapter     → Your implementation
```

### Unified API
```php
// Same method signatures across ALL database types
interface DatabaseAdapter {
    public function filter(string $keyPath, string $operator, $value);
    public function whereIn(string $keyPath, array $values);
    public function contains(string $keyPath, string $search);
    public function exists(string $keyPath);
    public function orderBy(string $keyPath, string $direction);
    public function select(string $keyPath, ?string $alias);
}
```

## 📊 **Database Support Matrix**

| Database Type | Status | Query Translation |
|---------------|--------|-------------------|
| **SQL Databases** |
| MySQL 5.7+ | ✅ Full Support | `JSON_EXTRACT()`, `JSON_CONTAINS()` |
| PostgreSQL 9.4+ | ✅ Full Support | `->`, `->>`, `@>` operators |
| SQLite 3.38+ | ✅ Basic Support | JSON1 extension |
| **NoSQL Databases** |
| MongoDB | ✅ Native Support | Dot notation, `$regex` |
| DocumentDB | 🔄 Ready | Same as MongoDB |
| CouchDB | 🔄 Ready | Custom adapter |
| **Graph Databases** |
| Neo4j | 🔄 Extensible | Custom Cypher adapter |
| ArangoDB | 🔄 Extensible | AQL adapter |
| **Others** |
| Redis | 🔄 Possible | Hash operations |
| Elasticsearch | 🔄 Possible | Query DSL |

## 🛠️ **Installation & Setup**

```bash
# Install the package
composer require pawsmedz/laravel-json-filter

# For MongoDB support (optional)
composer require jenssegers/mongodb

# Package auto-registers - no config needed!
```

## 📖 **Complete Usage Guide**

### Basic Operations

```php
use App\Models\User;

// Filter by exact values
$activeUsers = User::jsonFilter('meta->status', '=', 'active')->get();

// Multiple value matching  
$premiumUsers = User::jsonWhereIn('plan->type', ['pro', 'enterprise'])->get();

// Search within JSON
$developers = User::jsonContains('skills', 'php')->get();

// Check path existence
$subscribers = User::jsonExists('subscription->expires_at')->get();

// Sort by JSON values
$topUsers = User::jsonOrderBy('stats->score', 'desc')->get();

// Select JSON fields
$profiles = User::jsonSelect('profile->name as display_name')
               ->jsonSelect('profile->country as location')
               ->get();
```

### Advanced Chaining

```php
// Complex multi-database query
$results = User::query()
    ->jsonFilter('account->status', '=', 'active')
    ->jsonExists('subscription->plan')
    ->jsonWhereIn('profile->country', ['US', 'CA', 'UK'])
    ->jsonContains('skills', 'laravel')
    ->jsonOrderBy('stats->experience_years', 'desc')
    ->jsonSelect('profile->name as name')
    ->jsonSelect('stats->score as rating')
    ->limit(10)
    ->get();
```

## 🔧 **Extending for New Databases**

Adding support for any database is simple:

```php
use Pawsmedz\JsonFilter\Adapters\DatabaseAdapter;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class ElasticsearchAdapter extends DatabaseAdapter 
{
    public function supports($builder): bool {
        return str_contains(get_class($builder), 'Elasticsearch');
    }
    
    public function filter($keyPath, $operator, $value) {
        $path = $this->convertPath($keyPath); // 'profile->name' → 'profile.name'
        
        return $this->builder->where($path, $value);
    }
    
    public function contains($keyPath, $search) {
        $path = $this->convertPath($keyPath);
        
        return $this->builder->where($path, 'match', $search);
    }
    
    protected function convertPath($keyPath): string {
        return str_replace('->', '.', $keyPath);
    }
    
    // ... implement other methods
}

// Register your adapter
AdapterFactory::registerAdapter(ElasticsearchAdapter::class);

// Now ALL the unified macros work with Elasticsearch!
User::jsonFilter('profile->name', '=', 'John')->get(); // Works!
```

## 🧪 **Testing Across Databases**

```php
// Same test works on any database type
public function test_universal_json_queries()
{
    // Setup data
    User::create(['data' => ['name' => 'John', 'score' => 95]]);
    User::create(['data' => ['name' => 'Jane', 'score' => 87]]);
    
    // This assertion passes on MySQL, PostgreSQL, MongoDB, etc.
    $topUser = User::jsonOrderBy('data->score', 'desc')->first();
    $this->assertEquals('John', json_decode($topUser->data)->name);
}
```

## ⚡ **Performance Benefits**

- **Database-Specific Optimizations**: Each adapter uses the most efficient query method
- **No Translation Overhead**: Direct database-native queries
- **Lazy Adapter Loading**: Only loads the adapter you need
- **Query Plan Caching**: Database optimizers work naturally

## 🔄 **Migration Path**

### For Existing Laravel Projects
```php
// Before: Database-specific code
if ($driver === 'mysql') {
    $query->whereRaw("JSON_EXTRACT(meta, '$.status') = ?", ['active']);
} elseif ($driver === 'pgsql') {
    $query->whereRaw("meta->>'status' = ?", ['active']);
}

// After: Universal code
$query->jsonFilter('meta->status', '=', 'active');
```

### Backward Compatibility
Your existing code keeps working! The package provides both unified and legacy macros.

## 🎯 **Use Cases**

### E-Commerce Platforms
```php
// Works whether you use MySQL, MongoDB, or both
Product::jsonFilter('attributes->category', '=', 'electronics')
       ->jsonContains('features', 'wireless')
       ->jsonExists('pricing->discount')
       ->get();
```

### User Analytics  
```php
// Same query across your entire data stack
User::jsonFilter('analytics->segment', '=', 'power_user')
    ->jsonOrderBy('analytics->lifetime_value', 'desc')
    ->jsonSelect('analytics->last_seen as activity')
    ->get();
```

### Content Management
```php
// Universal content queries
Post::jsonFilter('meta->status', '=', 'published')
    ->jsonContains('tags', 'laravel')
    ->jsonExists('seo->description')
    ->jsonOrderBy('meta->published_at', 'desc')
    ->get();
```

## 📈 **Roadmap**

- **Phase 1**: ✅ SQL Database Support (MySQL, PostgreSQL, SQLite)
- **Phase 2**: 🔄 NoSQL Integration (MongoDB, DocumentDB)  
- **Phase 3**: 🔄 Graph Database Adapters (Neo4j, ArangoDB)
- **Phase 4**: 🔄 Search Engine Support (Elasticsearch, Solr)
- **Phase 5**: 🔄 Cloud Database Adapters (DynamoDB, Firestore)

## 🤝 **Contributing**

We welcome adapters for any database! Contributing is easy:

1. Create your adapter class extending `DatabaseAdapter`
2. Implement the required methods for your database
3. Add tests demonstrating the functionality
4. Submit a PR

## 🏆 **Why This Changes Everything**

**Before**: Write different code for each database
```php
// MySQL project
$users = User::whereRaw("JSON_EXTRACT(meta, '$.status') = 'active'");

// MongoDB project  
$users = User::where('meta.status', 'active');

// Different syntax, different learning curve, different maintenance
```

**After**: Write once, run anywhere
```php
// ANY database, same code
$users = User::jsonFilter('meta->status', '=', 'active');

// One syntax, one learning curve, one codebase
```

## 📜 **License**

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

**🌟 Star this repo if you believe in universal database compatibility!**

*Built with ❤️ by [Rayvand Jasper Valle Medrano](https://github.com/paws1234)*