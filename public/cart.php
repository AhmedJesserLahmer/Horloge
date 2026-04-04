<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$cartData = $cartService->details();
$items = $cartData['items'];
$subtotal = $cartData['subtotal'];

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="page-head">
    <h1>Your Cart</h1>
    <p><?= count($items) ?> item(s) ready for checkout.</p>
</section>

<?php if (empty($items)): ?>
    <div class="empty-state">
        <h2>Cart is empty</h2>
        <p>Browse our catalog and add some products first.</p>
        <a class="btn-link" href="<?= e(base_url('index.php')) ?>">Continue shopping</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <div>
            <?php foreach ($items as $item): ?>
                <article class="cart-item">
                    <img src="<?= e((string) $item['image_url']) ?>" alt="<?= e((string) $item['name']) ?>">
                    <div class="cart-item-content">
                        <h2><?= e((string) $item['name']) ?></h2>
                        <p><?= e(formatPrice((float) $item['price'])) ?> each</p>
                        <form method="post" action="<?= e(base_url('cart_actions.php')) ?>" class="inline-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                            <label for="qty-<?= (int) $item['id'] ?>">Qty</label>
                            <input id="qty-<?= (int) $item['id'] ?>" name="quantity" type="number" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>" required>
                            <button type="submit">Update</button>
                        </form>
                    </div>
                    <div class="cart-item-actions">
                        <strong><?= e(formatPrice((float) $item['line_total'])) ?></strong>
                        <form method="post" action="<?= e(base_url('cart_actions.php')) ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                            <button class="danger" type="submit">Remove</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="cart-summary">
            <h2>Summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <strong><?= e(formatPrice((float) $subtotal)) ?></strong>
            </div>
            <p class="summary-note">Shipping and tax are calculated at checkout.</p>
            <a class="btn-link" href="<?= e(base_url('checkout.php')) ?>">Proceed to checkout</a>
            <form method="post" action="<?= e(base_url('cart_actions.php')) ?>">
                <input type="hidden" name="action" value="clear">
                <button class="ghost" type="submit">Clear cart</button>
            </form>
        </aside>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
