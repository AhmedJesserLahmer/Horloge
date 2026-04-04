<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$products = $productModel->allActive();

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="hero">
    <p class="eyebrow">Trending Gear</p>
    <h1>Find your next favorite product.</h1>
    <p>Fast, simple, and modern shopping built on PHP + PDO.</p>
</section>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <h2>No products yet</h2>
        <p>Import the SQL seed file to add demo products.</p>
    </div>
<?php else: ?>
    <section class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <a class="card-media" href="<?= e(base_url('product.php?id=' . (int) $product['id'])) ?>">
                    <img src="<?= e((string) $product['image_url']) ?>" alt="<?= e((string) $product['name']) ?>">
                </a>
                <div class="card-content">
                    <h2>
                        <a href="<?= e(base_url('product.php?id=' . (int) $product['id'])) ?>">
                            <?= e((string) $product['name']) ?>
                        </a>
                    </h2>
                    <p><?= e((string) mb_strimwidth((string) $product['description'], 0, 120, '...')) ?></p>
                    <div class="card-bottom">
                        <strong><?= e(formatPrice((float) $product['price'])) ?></strong>
                        <form method="post" action="<?= e(base_url('add_to_cart.php')) ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit">Add to cart</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
