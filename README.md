# Laravel JSON Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/paws1234/laravel-json-filter/tests?label=tests&style=flat-square)](https://github.com/paws1234/laravel-json-filter/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)

A Laravel package that provides truly universal JSON/Document querying macros for ANY database type. The first Laravel package that works consistently across SQL, NoSQL, Graph, and document databases with a unified, expressive API.

## Features

- 🌍 **Truly Universal**: Works with ANY database - SQL (MySQL, PostgreSQL, SQLite), NoSQL (MongoDB, DocumentDB), and more
- 🔄 **Auto-Detection**: Automatically detects your database type and uses the optimal query strategy
- 🎯 **Unified API**: Same syntax works across all database types - no need to learn different query methods
- 🚀 **Extensible**: Easy to add new database adapters
- 🔧 **Zero Configuration**: Auto-discovery service provider with intelligent adapter selection
- 🧪 **Well Tested**: Comprehensive test suite across multiple database types  
- 📦 **Laravel Compatible**: Supports Laravel 9.x, 10.x, 11.x, and 12.x
- ⚡ **Performance Optimized**: Database-specific optimizations for each adapter

## Installation

### Basic Installation

Install the package via Composer:

```bash
composer require pawsmedz/laravel-json-filter
```

The package will automatically register its service provider via Laravel's auto-discovery feature.

### With MongoDB Support (Optional)

If you want to use MongoDB, install the package along with the appropriate MongoDB driver:

```bash
# Laravel 9.x with MongoDB
composer require pawsmedz/laravel-json-filter jenssegers/mongodb:^4.0

# Laravel 10.x - 11.x with MongoDB  
composer require pawsmedz/laravel-json-filter mongodb/laravel-mongodb:^4.0

# Laravel 12.x with MongoDB
composer require pawsmedz/laravel-json-filter mongodb/laravel-mongodb:^5.1
```

## Usage

### Basic JSON Filtering

Filter records based on JSON field values:

```php
use App\Models\User;

// Filter by JSON field value
$activeUsers = User::query()
    ->jsonFilter('meta->status', '=', 'active')
    ->get();

// Filter nested JSON paths
$proUsers = User::query()
    ->jsonFilter('meta->subscription->plan', '=', 'pro')
    ->get();
```

### JSON Where In

Filter records where JSON field matches any value in an array:

```php
// Find users with specific statuses
$users = User::query()
    ->jsonWhereIn('meta->status', ['active', 'pending'])
    ->get();

// Multiple subscription plans
$subscribers = User::query()
    ->jsonWhereIn('meta->subscription->plan', ['pro', 'enterprise'])
    ->get();
```

### JSON Contains (Search)

Search for records where JSON field contains a specific substring:

```php
// Search in JSON arrays or strings
$developers = User::query()
    ->jsonContains('meta->skills', 'php')
    ->get();

// Search in nested fields
$users = User::query()
    ->jsonContains('meta->profile->bio', 'developer')
    ->get();
```

### JSON Exists

Check if a JSON path exists in records:

```php
// Find users with subscription data
$subscribers = User::query()
    ->jsonExists('meta->subscription->plan')
    ->get();

// Check for nested paths
$profileUsers = User::query()
    ->jsonExists('meta->profile->preferences')
    ->get();
```

### JSON Select

Select specific JSON fields as columns:

```php
// Select JSON field with auto-generated alias
$users = User::query()
    ->jsonSelect('meta->status')
    ->get();

// Select with custom alias
$users = User::query()
    ->jsonSelect('meta->profile->country as user_country')
    ->get();

// Multiple JSON selections
$users = User::query()
    ->jsonSelect('meta->status')
    ->jsonSelect('meta->subscription->plan as plan')
    ->jsonSelect('meta->profile->country as country')
    ->get();
```

### JSON Order By

Order results by JSON field values:

```php
// Order by JSON field (ascending)
$users = User::query()
    ->jsonOrderBy('meta->score')
    ->get();

// Order by JSON field (descending)
$topUsers = User::query()
    ->jsonOrderBy('meta->score', 'desc')
    ->get();

// Complex ordering
$users = User::query()
    ->jsonOrderBy('meta->subscription->plan', 'desc')
    ->jsonOrderBy('meta->score', 'desc')
    ->get();
```

### Combining Multiple Macros

Chain multiple JSON operations for complex queries:

```php
$results = User::query()
    ->jsonFilter('meta->status', '=', 'active')
    ->jsonExists('meta->subscription')
    ->jsonWhereIn('meta->subscription->plan', ['pro', 'enterprise'])
    ->jsonContains('meta->skills', 'laravel')
    ->jsonSelect('meta->profile->name as display_name')
    ->jsonSelect('meta->subscription->plan as plan')
    ->jsonOrderBy('meta->score', 'desc')
    ->get();
```

### MongoDB Example

When using MongoDB (after installing the appropriate package), the same API works seamlessly:

```php
use MongoDB\Laravel\Eloquent\Model as Eloquent;

class MongoUser extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
}

// Same exact syntax works with MongoDB!
$mongoUsers = MongoUser::query()
    ->jsonFilter('profile.country', '=', 'US')           // Uses native MongoDB queries
    ->jsonContains('skills', 'php')                     // Regex search in MongoDB
    ->jsonExists('subscription.plan')                   // Field exists check
    ->jsonOrderBy('profile.score', 'desc')              // MongoDB sorting
    ->get();

// Complex MongoDB queries with the same API
$results = MongoUser::query()
    ->jsonFilter('status', '=', 'active')
    ->jsonWhereIn('subscription.plan', ['pro', 'enterprise'])
    ->jsonContains('bio', 'developer')
    ->jsonSelect('profile.name as name')
    ->jsonSelect('subscription.plan as plan')
    ->get();
```

## Universal Database Support

The package automatically detects your database type and uses the optimal query strategy:

### 🗄️ SQL Databases

#### MySQL
```sql
-- jsonFilter('meta->status', '=', 'active')
WHERE JSON_UNQUOTE(JSON_EXTRACT(meta, '$.status')) = 'active'
```

#### PostgreSQL
```sql
-- jsonFilter('meta->status', '=', 'active') 
WHERE meta->>'status' = 'active'
```

#### SQLite
```sql
-- Graceful fallback to LIKE operations
WHERE meta LIKE '%active%'
```

### 🍃 NoSQL Databases

#### MongoDB
```javascript
// jsonFilter('profile->country', '=', 'US')
{ "profile.country": "US" }

// jsonContains('skills', 'php')  
{ "skills": { $regex: /php/i } }
```

#### DocumentDB
```javascript
// Same syntax as MongoDB - seamless compatibility
{ "metadata.status": "active" }
```

### 🔄 Graph Databases
Support for Neo4j and ArangoDB can be added with custom adapters.

### 🗂️ Key-Value Stores  
Redis hash operations and DynamoDB attribute paths supported via adapters.

### ✨ The Magic
**One syntax, any database:**
```php
// This exact code works on MySQL, PostgreSQL, MongoDB, and more!
User::jsonFilter('profile->country', '=', 'US')
    ->jsonContains('skills', 'php')
    ->jsonExists('subscription->plan')
    ->get();
```

## Requirements

- PHP 8.1 or higher
- Laravel 9.x, 10.x, 11.x, or 12.x
- Any supported database (MySQL, PostgreSQL, SQLite, MongoDB, etc.)

## Optional Database Packages

### MongoDB Support

The package supports MongoDB as an optional feature. Install the appropriate MongoDB package based on your Laravel version:

```bash
# Laravel 9.x
composer require jenssegers/mongodb:^4.0

# Laravel 10.x - 11.x
composer require mongodb/laravel-mongodb:^4.0

# Laravel 12.x
composer require mongodb/laravel-mongodb:^5.1
```

**Note:** MongoDB support is completely optional. The package works perfectly with MySQL, PostgreSQL, and SQLite without any additional dependencies.

### Other NoSQL Databases

Support for additional NoSQL databases can be added with custom adapters:

```bash
# More database packages as needed when adapters are available
```

## Extending Support

Add support for new databases by creating custom adapters:

```php
use Pawsmedz\JsonFilter\Adapters\DatabaseAdapter;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class MyCustomAdapter extends DatabaseAdapter {
    public function supports($builder): bool {
        // Detection logic
        return str_contains(get_class($builder->getConnection()), 'MyDB');
    }
    
    public function filter($keyPath, $operator, $value) {
        // Custom query logic
        return $this->builder->where($this->convertPath($keyPath), $operator, $value);
    }
    
    // Implement other methods...
}

// Register your adapter
AdapterFactory::registerAdapter(MyCustomAdapter::class);
```

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review our security policy on how to report security vulnerabilities.

## Credits

- [Rayvand Jasper Valle Medrano](https://github.com/paws1234)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
