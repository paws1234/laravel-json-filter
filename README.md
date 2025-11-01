# Laravel JSON Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/paws1234/laravel-json-filter/tests?label=tests&style=flat-square)](https://github.com/paws1234/laravel-json-filter/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pawsmedz/laravel-json-filter.svg?style=flat-square)](https://packagist.org/packages/pawsmedz/laravel-json-filter)

A Laravel package that provides fluent, database-agnostic JSON querying macros for Eloquent and Query Builder. Seamlessly work with JSON columns across MySQL, PostgreSQL, and SQLite with a consistent, expressive API.

## Features

- 🔍 **Database Agnostic**: Works seamlessly with MySQL, PostgreSQL, and SQLite
- 🎯 **Fluent API**: Clean, expressive syntax for JSON queries
- 🚀 **Multiple Macros**: Filter, select, order, search, and check existence
- 🔧 **Easy Integration**: Auto-discovery service provider
- 🧪 **Well Tested**: Comprehensive test suite
- 📦 **Laravel Compatible**: Supports Laravel 9.x, 10.x, and 11.x

## Installation

You can install the package via Composer:

```bash
composer require pawsmedz/laravel-json-filter
```

The package will automatically register its service provider via Laravel's auto-discovery feature.

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

## Database Support

The package automatically detects your database driver and uses the appropriate JSON syntax:

### MySQL
```sql
-- jsonFilter('meta->status', '=', 'active')
WHERE JSON_UNQUOTE(JSON_EXTRACT(meta, '$.status')) = 'active'

-- jsonSelect('meta->status as user_status')
SELECT JSON_UNQUOTE(JSON_EXTRACT(meta, '$.status')) as user_status
```

### PostgreSQL
```sql
-- jsonFilter('meta->status', '=', 'active')
WHERE meta->>'status' = 'active'

-- jsonSelect('meta->status as user_status')
SELECT meta->>'status' as user_status
```

### SQLite
Falls back to basic column operations for compatibility.

## Requirements

- PHP 8.1 or higher
- Laravel 9.x, 10.x, or 11.x
- MySQL, PostgreSQL, or SQLite database

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
