<?php
declare(strict_types = 1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $value = $_ENV[$key] ?? false;
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}
