<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

final class CartService
{
    public function __construct(private Product $productModel)
    {
        $this->init();
    }

    public function init(): void
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $this->init();
        $quantity = max(1, $quantity);

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
            return;
        }

        $_SESSION['cart'][$productId] = $quantity;
    }

    public function update(int $productId, int $quantity): void
    {
        $this->init();

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $_SESSION['cart'][$productId] = $quantity;
    }

    public function remove(int $productId): void
    {
        $this->init();
        unset($_SESSION['cart'][$productId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function count(): int
    {
        $this->init();
        return (int) array_sum($_SESSION['cart']);
    }

    public function details(): array
    {
        $this->init();

        if (empty($_SESSION['cart'])) {
            return ['items' => [], 'subtotal' => 0.0];
        }

        $productIds = array_keys($_SESSION['cart']);
        $products = $this->productModel->findActiveByIds($productIds);

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
}
