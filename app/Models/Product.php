<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Product
{
    public function __construct(private PDO $pdo) {}

    public function allActive(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, description, price, image_url, stock FROM products WHERE is_active = 1 ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findActiveById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, description, price, image_url, stock FROM products WHERE id = :id AND is_active = 1 LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findActiveByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT id, name, price, image_url, stock FROM products WHERE id IN ($placeholders) AND is_active = 1");
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }

    public function decrementStock(int $id, int $quantity): bool
    {
        $stmt = $this->pdo->prepare('UPDATE products SET stock = stock - :quantity_sub WHERE id = :id AND stock >= :quantity_min');
        $stmt->execute([
            'id' => $id,
            'quantity_sub' => $quantity,
            'quantity_min' => $quantity,
        ]);

        return $stmt->rowCount() === 1;
    }
}
