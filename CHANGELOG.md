# Changelog

All notable changes to `laravel-json-filter` will be documented in this file.

## [Unreleased]

### Added
- Laravel 12.x support
- Extended PHP version support to include PHP 8.3
- Updated Orchestra Testbench support for Laravel 12 testing
- Enhanced CI/CD pipeline to test against Laravel 12

### Changed
- Updated composer dependencies to support Laravel 12.x
- Expanded GitHub Actions matrix to include Laravel 12 testing
- Updated documentation to reflect Laravel 12 compatibility

### Compatibility
- PHP: ^8.1|^8.2|^8.3
- Laravel: ^9.0|^10.0|^11.0|^12.0
- Orchestra Testbench: ^7.0|^8.0|^9.0|^10.0
- PHPUnit: ^9.6|^10.0|^11.0

## [1.0.0] - Previous Release

### Added
- Initial release with JSON filtering capabilities
- Support for MySQL, PostgreSQL, and SQLite
- Comprehensive macro system (jsonFilter, jsonSelect, jsonOrderBy, etc.)
- Laravel 9.x, 10.x, and 11.x support
- Auto-discovery service provider