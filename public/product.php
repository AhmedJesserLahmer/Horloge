<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    Session::setFlash('error', 'Invalid product id.');
    redirect('index.php');
}

$product = $productModel->findActiveById($id);
if ($product === null) {
    Session::setFlash('error', 'Product not found.');
    redirect('index.php');
}

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="product-detail">
    <div class="detail-image">
        <img src="<?= e((string) $product['image_url']) ?>" alt="<?= e((string) $product['name']) ?>">
    </div>
    <div class="detail-content">
        <p class="eyebrow">In Stock: <?= (int) $product['stock'] ?></p>
        <h1><?= e((string) $product['name']) ?></h1>
        <p><?= e((string) $product['description']) ?></p>
        <p class="big-price"><?= e(formatPrice((float) $product['price'])) ?></p>

        <form method="post" action="<?= e(base_url('add_to_cart.php')) ?>" class="inline-form">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <label for="quantity">Qty</label>
            <input id="quantity" name="quantity" type="number" min="1" max="<?= (int) $product['stock'] ?>" value="1" required>
            <button type="submit">Add to cart</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
