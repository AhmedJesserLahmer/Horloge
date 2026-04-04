<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($productId === false || $productId === null || $productId < 1) {
    Session::setFlash('error', 'Invalid product.');
    redirect('index.php');
}

$product = $productModel->findActiveById($productId);
if ($product === null) {
    Session::setFlash('error', 'Product not available.');
    redirect('index.php');
}

$quantity = ($quantity === false || $quantity === null || $quantity < 1) ? 1 : $quantity;
$quantity = min($quantity, (int) $product['stock']);

if ($quantity < 1) {
    Session::setFlash('error', 'This product is out of stock.');
    redirect('index.php');
}

$cartService->add($productId, $quantity);
Session::setFlash('success', 'Added to cart.');
redirect('cart.php');
