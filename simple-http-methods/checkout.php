<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$method = effectiveMethod();
$items = cartItems();
$total = cartTotal();
$message = null;

if ($method === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));

    if ($name === '' || $email === '') {
        $message = 'Please fill name and email.';
    } elseif (empty($items)) {
        $message = 'Cart is empty.';
    } else {
        clearCart();
        flash('Order placed successfully using POST.');
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <a href="index.php">&larr; Home</a>
        <h1>Checkout</h1>
        <p class="badge">This page is loaded with: <?= h($method) ?></p>

        <?php if ($message !== null): ?>
            <div class="alert"><?= h($message) ?></div>
        <?php endif; ?>

        <p>Total to pay: <strong><?= h(money($total)) ?></strong></p>

        <form method="post" action="checkout.php" class="card">
            <label>
                Name
                <input type="text" name="name" required>
            </label>
            <label>
                Email
                <input type="email" name="email" required>
            </label>
            <button type="submit">Place order (POST)</button>
        </form>
    </div>
</body>

</html>