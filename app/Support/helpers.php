<?php

declare(strict_types=1);

use App\Core\Config;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function base_url(string $path = ''): string
{
    $base = rtrim((string) Config::get('app_base_url', ''), '/');
    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function asset_url(string $path): string
{
    $root = rtrim((string) Config::get('app_root_url', ''), '/');
    return $root . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}
