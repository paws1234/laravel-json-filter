# Changelog

All notable changes to `laravel-json-filter` will be documented in this file.

## [1.3.1] - 2024-12-19

### Added
- PHP 8.4 support with full Laravel 9-12 compatibility
- Laravel 12 support with comprehensive testing
- Enhanced GitHub Actions testing matrix with PHP 8.4 and multi-database support
- MongoDB support as optional feature with version-specific installation:
  - Laravel 9.x: `composer require jenssegers/mongodb:^4.0`
  - Laravel 10.x-11.x: `composer require mongodb/laravel-mongodb:^4.0`
  - Laravel 12.x: `composer require mongodb/laravel-mongodb:^5.1`
- Unified database adapter architecture supporting MySQL, PostgreSQL, SQLite, and MongoDB

### Changed
- MongoDB support is now optional to ensure broader compatibility across Laravel versions
- Updated composer suggestions with proper MongoDB package recommendations
- Improved adapter factory resilience when optional packages are not installed

### Fixed
- PHPUnit 11 compatibility issues (resolved all 12 deprecation warnings)
- Dependency conflicts between Laravel versions and MongoDB packages
- Cross-database compatibility improvements

### Compatibility
- PHP: ^8.1|^8.2|^8.3|^8.4
- Laravel: ^9.0|^10.0|^11.0|^12.0
- Orchestra Testbench: ^7.0|^8.0|^9.0|^10.0
- PHPUnit: ^9.6|^10.0|^11.0

### MongoDB Installation (Optional)
MongoDB support is completely optional. To enable MongoDB features:
```bash
# Choose based on your Laravel version
composer require jenssegers/mongodb:^4.0        # Laravel 9.x
composer require mongodb/laravel-mongodb:^4.0   # Laravel 10.x-11.x  
composer require mongodb/laravel-mongodb:^5.1   # Laravel 12.x
```

## [1.0.0] - Previous Release

### Added
- Initial release with JSON filtering capabilities
- Support for MySQL, PostgreSQL, and SQLite
- Comprehensive macro system (jsonFilter, jsonSelect, jsonOrderBy, etc.)
- Laravel 9.x, 10.x, and 11.x support
- Auto-discovery service provider