<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT) ?? '';
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if ($action === 'clear') {
    $cartService->clear();
    Session::setFlash('success', 'Cart cleared.');
    redirect('cart.php');
}

if ($productId === false || $productId === null || $productId < 1) {
    Session::setFlash('error', 'Invalid cart action.');
    redirect('cart.php');
}

if ($action === 'remove') {
    $cartService->remove($productId);
    Session::setFlash('success', 'Item removed.');
    redirect('cart.php');
}

if ($action === 'update') {
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    if ($quantity === false || $quantity === null) {
        $quantity = 1;
    }

    $product = $productModel->findActiveById($productId);
    if ($product === null) {
        $cartService->remove($productId);
        Session::setFlash('error', 'Product no longer available.');
        redirect('cart.php');
    }

    $quantity = min($quantity, (int) $product['stock']);
    $cartService->update($productId, $quantity);
    Session::setFlash('success', 'Cart updated.');
}

redirect('cart.php');
