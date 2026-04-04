<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="success-box">
    <h1>Order Placed Successfully</h1>
    <p>Thanks for shopping with <?= e((string) App\Core\Config::get('app_name')) ?>.</p>
    <?php if ($orderId !== false && $orderId !== null): ?>
        <p>Your order number is <strong>#<?= (int) $orderId ?></strong>.</p>
    <?php endif; ?>
    <a class="btn-link" href="<?= e(base_url('index.php')) ?>">Back to shop</a>
</section>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
