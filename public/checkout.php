<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

if (!$authService->check()) {
    Session::setFlash('error', 'Please log in to continue.');
    redirect('login.php');
}

$currentUser = $authService->user();
$cartData = $cartService->details();
$items = $cartData['items'];
$subtotal = $cartData['subtotal'];

if (empty($items)) {
    Session::setFlash('error', 'Your cart is empty.');
    redirect('index.php');
}

$errors = [];
$form = [
    'customer_name' => (string) ($currentUser['full_name'] ?? ''),
    'customer_email' => (string) ($currentUser['email'] ?? ''),
    'customer_phone' => (string) ($currentUser['phone'] ?? ''),
    'shipping_address' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['customer_name'] = trim((string) (filter_input(INPUT_POST, 'customer_name', FILTER_DEFAULT) ?? ''));
    $form['customer_email'] = trim((string) (filter_input(INPUT_POST, 'customer_email', FILTER_VALIDATE_EMAIL) ?: ''));
    $form['customer_phone'] = trim((string) (filter_input(INPUT_POST, 'customer_phone', FILTER_DEFAULT) ?? ''));
    $form['shipping_address'] = trim((string) (filter_input(INPUT_POST, 'shipping_address', FILTER_DEFAULT) ?? ''));

    if ($form['customer_name'] === '') {
        $errors[] = 'Customer name is required.';
    }
    if ($form['customer_email'] === '') {
        $errors[] = 'Valid email is required.';
    }
    if ($form['shipping_address'] === '') {
        $errors[] = 'Shipping address is required.';
    }

    if (empty($errors)) {
        $latestCart = $cartService->details();
        if (empty($latestCart['items'])) {
            $errors[] = 'Your cart became empty during checkout.';
        } else {
            try {
                $orderId = $orderModel->createWithItems([
                    'user_id' => (int) ($currentUser['id'] ?? 0),
                    'customer_name' => $form['customer_name'],
                    'customer_email' => $form['customer_email'],
                    'customer_phone' => $form['customer_phone'],
                    'shipping_address' => $form['shipping_address'],
                ], $latestCart['items'], (float) $latestCart['subtotal']);

                $cartService->clear();
                redirect('order_success.php?order_id=' . $orderId);
            } catch (Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }
    }
}

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="page-head">
    <h1>Checkout</h1>
    <p>Complete your order in one final step.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="checkout-layout">
    <form class="checkout-form" method="post" action="<?= e(base_url('checkout.php')) ?>">
        <label for="customer_name">Full Name</label>
        <input id="customer_name" name="customer_name" type="text" value="<?= e($form['customer_name']) ?>" required>

        <label for="customer_email">Email</label>
        <input id="customer_email" name="customer_email" type="email" value="<?= e($form['customer_email']) ?>" required>

        <label for="customer_phone">Phone</label>
        <input id="customer_phone" name="customer_phone" type="text" value="<?= e($form['customer_phone']) ?>">

        <label for="shipping_address">Shipping Address</label>
        <textarea id="shipping_address" name="shipping_address" rows="4" required><?= e($form['shipping_address']) ?></textarea>

        <button type="submit">Place order</button>
    </form>

    <aside class="cart-summary">
        <h2>Order Summary</h2>
        <?php foreach ($items as $item): ?>
            <div class="summary-row small">
                <span><?= (int) $item['quantity'] ?>x <?= e((string) $item['name']) ?></span>
                <strong><?= e(formatPrice((float) $item['line_total'])) ?></strong>
            </div>
        <?php endforeach; ?>
        <hr>
        <div class="summary-row">
            <span>Total</span>
            <strong><?= e(formatPrice((float) $subtotal)) ?></strong>
        </div>
    </aside>
</div>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
