<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Session;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AuthService;
use App\Services\CartService;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

Session::start();

require_once __DIR__ . '/../app/Support/helpers.php';

$pdo = Database::connection();
$productModel = new Product($pdo);
$userModel = new User($pdo);
$cartService = new CartService($productModel);
$authService = new AuthService($userModel);
$orderModel = new Order($pdo, $productModel);
$flash = Session::pullFlash();
$currentUser = $authService->user();
