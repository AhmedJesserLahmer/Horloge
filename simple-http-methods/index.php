<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$currentMethod = effectiveMethod();
$message = flash();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple HTTP Methods Demo</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <h1>Beginner HTTP Methods Demo</h1>
        <p class="badge">Current request method: <?= h($currentMethod) ?></p>

        <?php if ($message !== null): ?>
            <div class="alert"><?= h($message) ?></div>
        <?php endif; ?>

        <p>This folder is a simplified mini-shop focused on understanding HTTP methods.</p>

        <ul>
            <li><a href="products.php">Products (GET)</a></li>
            <li><a href="cart.php">Cart (GET + POST)</a></li>
            <li><a href="checkout.php">Checkout (GET + POST)</a></li>
            <li><a href="http_methods_lab.php">HTTP Methods Lab (GET/POST/PUT/DELETE)</a></li>
        </ul>
    </div>
</body>

</html>