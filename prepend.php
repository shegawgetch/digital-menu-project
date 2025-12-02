<?php

// Prevent old server.php errors in Laravel 11 on Windows
if (PHP_SAPI === 'cli' && file_exists(__DIR__.'/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php')) {
    require __DIR__.'/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';
}
// Otherwise do nothing
