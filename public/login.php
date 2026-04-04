<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

if ($authService->check()) {
    redirect('index.php');
}

$errors = [];
$form = [
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['email'] = trim((string) (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: ''));
    $password = (string) (filter_input(INPUT_POST, 'password', FILTER_DEFAULT) ?? '');

    if ($form['email'] === '') {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $ok = $authService->attempt($form['email'], $password);
        if (!$ok) {
            $errors[] = 'Invalid email or password.';
        } else {
            Session::setFlash('success', 'Welcome back.');
            redirect('index.php');
        }
    }
}

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="page-head">
    <h1>Login</h1>
    <p>Access your account to continue checkout.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="checkout-form auth-form" method="post" action="<?= e(base_url('login.php')) ?>">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= e($form['email']) ?>" required>

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>

    <button type="submit">Login</button>
    <p class="auth-hint">Need an account? <a href="<?= e(base_url('signup.php')) ?>">Sign up here</a>.</p>
</form>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
