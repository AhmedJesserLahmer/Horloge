<?php

declare(strict_types=1);

use App\Core\Config;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e((string) Config::get('app_name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/style.css')) ?>">
</head>

<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="<?= e(base_url('index.php')) ?>">
                <span class="brand-logo">NM</span>
                <span class="brand-text"><?= e((string) Config::get('app_name')) ?></span>
            </a>
            <nav class="nav-links">
                <a href="<?= e(base_url('index.php')) ?>">Shop</a>
                <a href="<?= e(base_url('cart.php')) ?>">Cart (<?= $cartService->count() ?>)</a>
                <?php if ($currentUser !== null): ?>
                    <span class="nav-user">Hi, <?= e((string) $currentUser['full_name']) ?></span>
                    <a href="<?= e(base_url('logout.php')) ?>">Logout</a>
                <?php else: ?>
                    <a href="<?= e(base_url('login.php')) ?>">Login</a>
                    <a href="<?= e(base_url('signup.php')) ?>">Sign up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content container">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= e((string) $flash['type']) ?>">
                <?= e((string) $flash['message']) ?>
            </div>
        <?php endif; ?>