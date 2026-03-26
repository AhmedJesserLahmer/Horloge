<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use RuntimeException;
use Throwable;

final class Order
{
    public function __construct(private PDO $pdo, private Product $productModel) {}

    public function createWithItems(array $customer, array $items, float $totalAmount): int
    {
        try {
            $this->pdo->beginTransaction();

            $insertOrder = $this->pdo->prepare(
                'INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, shipping_address, total_amount, status) VALUES (:user_id, :customer_name, :customer_email, :customer_phone, :shipping_address, :total_amount, :status)'
            );
            $insertOrder->execute([
                'user_id' => (int) $customer['user_id'],
                'customer_name' => $customer['customer_name'],
                'customer_email' => $customer['customer_email'],
                'customer_phone' => $customer['customer_phone'],
                'shipping_address' => $customer['shipping_address'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $orderId = (int) $this->pdo->lastInsertId();

            $insertOrderItem = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total) VALUES (:order_id, :product_id, :quantity, :unit_price, :line_total)'
            );

            foreach ($items as $item) {
                $insertOrderItem->execute([
                    'order_id' => $orderId,
                    'product_id' => (int) $item['id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                    'line_total' => (float) $item['line_total'],
                ]);

                $updated = $this->productModel->decrementStock((int) $item['id'], (int) $item['quantity']);
                if (!$updated) {
                    throw new RuntimeException('One item is out of stock. Please update your cart.');
                }
            }

            $this->pdo->commit();

            return $orderId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }
}
