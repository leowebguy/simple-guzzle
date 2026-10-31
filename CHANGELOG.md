# Release Notes for Simple Guzzle

All notable changes to this project will be documented in this file.

## 2.1.0 - 2026.10.30
### Added
- Compatibility with PHP ^8.2 || ^8.3 || ^8.4 || ^8.5 and Craft CMS ^5.0.0.
- PHPStan static analysis configuration (`phpstan.neon`) at Level 8.
- Strict native PHP 8.2+ parameter and return type declarations across services and Twig extensions.

### Changed
- Modernized `composer.json` schema to use `authors` array and compliant `extra` metadata.
- Optimized response parsing to handle 2xx range HTTP responses and prevent multiple stream reads on PSR-7 bodies.
- Fixed cache bypass behavior so that passing a duration of `0` (or omitting cache duration) correctly bypasses caching as documented, rather than caching indefinitely.
- Code style normalized to PSR-12 using Laravel Pint.

## 2.0.0 - 2024.03.26
Craft 5, PHP 8.2

## 1.0.1 - 2023.05.13
Class tweaks

## 1.0.0 - 2023.04.19
Initial release
