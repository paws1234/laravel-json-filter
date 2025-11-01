<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Test Environment Bootstrap
|--------------------------------------------------------------------------
| This file ensures a consistent testing environment for Orchestra Testbench
| without relying on Facades (since Laravel hasn't booted yet).
| We'll configure minimal defaults and defer full initialization to TestCase.
|--------------------------------------------------------------------------
*/

// Load environment variables
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->safeLoad();
}

// Define fallback defaults if not provided via env()
putenv('DB_CONNECTION=' . ($_ENV['DB_CONNECTION'] ?? 'sqlite'));
putenv('DB_DATABASE=' . ($_ENV['DB_DATABASE'] ?? ':memory:'));
putenv('DB_USERNAME=' . ($_ENV['DB_USERNAME'] ?? 'root'));
putenv('DB_PASSWORD=' . ($_ENV['DB_PASSWORD'] ?? ''));

