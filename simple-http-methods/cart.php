<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$method = effectiveMethod();

if ($method === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $productId = (int) ($_POST['product_id'] ?? 0);
    $qty = (int) ($_POST['qty'] ?? 1);

    if ($action === 'add' && $productId > 0) {
        addToCart($productId, $qty);
        flash('Product added to cart using POST.');
    }

    if ($action === 'remove' && $productId > 0) {
        removeFromCart($productId);
        flash('Product removed from cart using POST.');
    }

    if ($action === 'clear') {
        clearCart();
        flash('Cart cleared using POST.');
    }

    header('Location: cart.php');
    exit;
}

$message = flash();
$items = cartItems();
$total = cartTotal();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <a href="index.php">&larr; Home</a>
        <h1>Cart</h1>
        <p class="badge">This page is loaded with: <?= h($method) ?></p>

        <?php if ($message !== null): ?>
            <div class="alert"><?= h($message) ?></div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="card">
                    <strong><?= h($item['name']) ?></strong>
                    <p><?= (int) $item['qty'] ?> x <?= h(money((float) $item['price'])) ?> = <?= h(money((float) $item['line'])) ?></p>

                    <form method="post" action="cart.php">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                        <button type="submit">Remove (POST)</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <p><strong>Total: <?= h(money($total)) ?></strong></p>

            <form method="post" action="cart.php">
                <input type="hidden" name="action" value="clear">
                <button type="submit">Clear Cart (POST)</button>
            </form>

            <p><a href="checkout.php">Go to checkout</a></p>
        <?php endif; ?>

        <p><a href="products.php">Continue shopping</a></p>
    </div>
</body>

</html>