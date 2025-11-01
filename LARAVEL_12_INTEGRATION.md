# Laravel 12 Integration Guide

This guide covers the Laravel 12 integration updates for the `pawsmedz/laravel-json-filter` package.

## What's New in Laravel 12 Support

### ✅ Full Compatibility
The package now fully supports Laravel 12.x alongside existing support for Laravel 9.x, 10.x, and 11.x.

### ✅ Enhanced Testing
- Extended CI/CD pipeline to include Laravel 12 testing
- Updated Orchestra Testbench to version 10.x for Laravel 12 compatibility
- All existing functionality tested and verified on Laravel 12

### ✅ Future-Proof Dependencies
- PHP 8.1, 8.2, and 8.3 support
- PHPUnit 11.x support for modern testing
- Updated dependency constraints for better compatibility

## Upgrading to Laravel 12

### 1. Update Your Project
```bash
# Upgrade Laravel to version 12
composer require laravel/framework:^12.0

# Update the JSON Filter package
composer update pawsmedz/laravel-json-filter
```

### 2. Verify Compatibility
The package automatically works with Laravel 12 - no code changes required!

```php
// This works exactly the same in Laravel 12
User::query()
    ->jsonFilter('meta->status', '=', 'active')
    ->jsonSelect('meta->profile->name as display_name')
    ->jsonOrderBy('meta->score', 'desc')
    ->get();
```

### 3. Test Your Application
Run your existing tests to ensure everything works:

```bash
php artisan test
```

## New in This Release

### Updated Dependencies
```json
{
    "require": {
        "php": "^8.1|^8.2|^8.3",
        "illuminate/database": "^9.0|^10.0|^11.0|^12.0",
        "illuminate/support": "^9.0|^10.0|^11.0|^12.0"
    },
    "require-dev": {
        "orchestra/testbench": "^7.0|^8.0|^9.0|^10.0",
        "phpunit/phpunit": "^9.6|^10.0|^11.0"
    }
}
```

### Enhanced CI/CD Matrix
Our GitHub Actions now test against:
- **PHP Versions**: 8.1, 8.2, 8.3
- **Laravel Versions**: 9.*, 10.*, 11.*, 12.*
- **Databases**: MySQL, PostgreSQL
- **Operating Systems**: Ubuntu (Linux)

## Laravel 12 Specific Features

### JSON Performance Improvements
Laravel 12 includes performance improvements for JSON operations which this package automatically benefits from:

- Faster JSON parsing in MySQL
- Optimized JSONB operations in PostgreSQL  
- Enhanced query caching for JSON columns

### Modern PHP Features
With Laravel 12's PHP 8.1+ requirement, the package leverages:
- Union types for better type safety
- Enum support for better constant management
- Fibers for potential async operations

## Backward Compatibility

### ✅ No Breaking Changes
- All existing method signatures remain the same
- Same API across Laravel 9, 10, 11, and 12
- Existing applications can upgrade seamlessly

### ✅ Database Support Unchanged
- MySQL: JSON_EXTRACT and JSON functions
- PostgreSQL: JSONB operators (->, ->>)
- SQLite: Fallback compatibility mode

## Migration Checklist

- [ ] Upgrade Laravel to version 12
- [ ] Update composer dependencies
- [ ] Run existing tests
- [ ] Verify JSON filtering still works
- [ ] Deploy with confidence!

## Support Matrix

| Laravel Version | PHP Version | Package Version | Support Status |
|----------------|-------------|-----------------|----------------|
| 9.x | 8.1+ | Latest | ✅ Active |
| 10.x | 8.1+ | Latest | ✅ Active |
| 11.x | 8.2+ | Latest | ✅ Active |
| 12.x | 8.1+ | Latest | ✅ Active |

## Getting Help

If you encounter any issues with Laravel 12 integration:

1. Check the [Issues](https://github.com/paws1234/laravel-json-filter/issues) page
2. Run the test suite to verify functionality
3. Create a new issue with Laravel 12 reproduction steps

## What's Next

Future releases will continue to support Laravel 12 while maintaining backward compatibility with earlier versions. We're committed to keeping this package updated with the latest Laravel features and performance improvements.