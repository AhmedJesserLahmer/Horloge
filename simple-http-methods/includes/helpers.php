<?php

declare(strict_types=1);

require_once __DIR__ . '/data.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function money(float $value): string
{
    return '$' . number_format($value, 2);
}

function effectiveMethod(): string
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    if ($method === 'POST' && isset($_POST['_method'])) {
        $override = strtoupper((string) $_POST['_method']);
        if (in_array($override, ['PUT', 'DELETE'], true)) {
            return $override;
        }
    }

    return $method;
}

function findProduct(int $id): ?array
{
    foreach (demoProducts() as $product) {
        if ((int) $product['id'] === $id) {
            return $product;
        }
    }

    return null;
}

function initCart(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart(int $productId, int $qty): void
{
    initCart();
    $qty = max(1, $qty);

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function removeFromCart(int $productId): void
{
    initCart();
    unset($_SESSION['cart'][$productId]);
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function cartItems(): array
{
    initCart();

    $items = [];
    foreach ($_SESSION['cart'] as $id => $qty) {
        $product = findProduct((int) $id);
        if ($product === null) {
            continue;
        }

        $line = (float) $product['price'] * (int) $qty;
        $items[] = [
            'id' => (int) $id,
            'name' => (string) $product['name'],
            'price' => (float) $product['price'],
            'qty' => (int) $qty,
            'line' => $line,
        ];
    }

    return $items;
}

function cartTotal(): float
{
    $total = 0.0;
    foreach (cartItems() as $item) {
        $total += (float) $item['line'];
    }

    return $total;
}

function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash_message'] = $message;
        return null;
    }

    if (!isset($_SESSION['flash_message'])) {
        return null;
    }

    $msg = (string) $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $msg;
}
