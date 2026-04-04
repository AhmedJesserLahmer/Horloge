<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(APP_BASE_URL) ?>/assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="<?= e(APP_BASE_URL) ?>/index.php">
                <span class="brand-logo">NM</span>
                <span class="brand-text"><?= e(APP_NAME) ?></span>
            </a>
            <nav class="nav-links">
                <a href="<?= e(APP_BASE_URL) ?>/index.php">Shop</a>
                <a href="<?= e(APP_BASE_URL) ?>/cart.php">Cart (<?= getCartCount() ?>)</a>
                <?php if ($currentUser !== null): ?>
                    <span class="nav-user">Hi, <?= e((string) $currentUser['full_name']) ?></span>
                    <a href="<?= e(APP_BASE_URL) ?>/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?= e(APP_BASE_URL) ?>/login.php">Login</a>
                    <a href="<?= e(APP_BASE_URL) ?>/signup.php">Sign up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content container">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>