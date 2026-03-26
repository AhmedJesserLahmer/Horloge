<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$method = effectiveMethod();
$products = demoProducts();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <a href="index.php">&larr; Home</a>
        <h1>Products</h1>
        <p class="badge">This page is loaded with: <?= h($method) ?></p>

        <?php foreach ($products as $p): ?>
            <div class="card">
                <h3><?= h((string) $p['name']) ?></h3>
                <p>Price: <?= h(money((float) $p['price'])) ?></p>

                <form method="post" action="cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                    <label>
                        Qty
                        <input type="number" name="qty" value="1" min="1" required>
                    </label>
                    <button type="submit">Add to cart (POST)</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>