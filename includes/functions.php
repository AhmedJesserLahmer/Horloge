<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function getCurrentUser(): ?array
{
    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }

    return $_SESSION['user'];
}

function isLoggedIn(): bool
{
    return getCurrentUser() !== null;
}

function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    setFlash('error', 'Please log in to continue.');
    redirect('login.php');
}

function getUserByEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT id, full_name, email, phone, password_hash, created_at FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function createUser(PDO $pdo, string $fullName, string $email, string $password, string $phone = ''): int
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, phone, password_hash) VALUES (:full_name, :email, :phone, :password_hash)'
    );
    $stmt->execute([
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'password_hash' => $passwordHash,
    ]);

    return (int) $pdo->lastInsertId();
}

function loginUser(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => (string) $user['full_name'],
        'email' => (string) $user['email'],
        'phone' => (string) ($user['phone'] ?? ''),
    ];
}

function logoutUser(): void
{
    unset($_SESSION['user']);
}

function getActiveProducts(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, name, description, price, image_url, stock FROM products WHERE is_active = 1 ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function getProductById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, name, description, price, image_url, stock FROM products WHERE id = :id AND is_active = 1 LIMIT 1');
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    return $product ?: null;
}

function initCart(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart(int $productId, int $quantity = 1): void
{
    initCart();

    if ($quantity < 1) {
        $quantity = 1;
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function updateCartItem(int $productId, int $quantity): void
{
    initCart();

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
        return;
    }

    $_SESSION['cart'][$productId] = $quantity;
}

function removeCartItem(int $productId): void
{
    initCart();
    unset($_SESSION['cart'][$productId]);
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function getCartCount(): int
{
    initCart();
    return (int) array_sum($_SESSION['cart']);
}

function getCartItems(PDO $pdo): array
{
    initCart();

    if (empty($_SESSION['cart'])) {
        return ['items' => [], 'subtotal' => 0.0];
    }

    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $stmt = $pdo->prepare("SELECT id, name, price, image_url, stock FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();

    $items = [];
    $subtotal = 0.0;

    foreach ($products as $product) {
        $id = (int) $product['id'];
        $qty = (int) ($_SESSION['cart'][$id] ?? 0);
        if ($qty < 1) {
            continue;
        }

        $qty = min($qty, (int) $product['stock']);
        if ($qty < 1) {
            continue;
        }

        $_SESSION['cart'][$id] = $qty;
        $lineTotal = $qty * (float) $product['price'];
        $subtotal += $lineTotal;

        $items[] = [
            'id' => $id,
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'image_url' => $product['image_url'],
            'quantity' => $qty,
            'stock' => (int) $product['stock'],
            'line_total' => $lineTotal,
        ];
    }

    return ['items' => $items, 'subtotal' => $subtotal];
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function redirect(string $path): void
{
    header('Location: ' . APP_BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
