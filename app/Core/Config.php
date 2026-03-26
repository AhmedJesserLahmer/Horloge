<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private const SETTINGS = [
        'db_host' => '127.0.0.1',
        'db_port' => 3307,
        'db_name' => 'ecommerce_db',
        'db_user' => 'root',
        'db_pass' => '',
        'app_name' => 'NovaMart',
        'app_root_url' => '/E-commerce',
        'app_base_url' => '/E-commerce/public',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::SETTINGS[$key] ?? $default;
    }
}
