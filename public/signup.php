<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

if ($authService->check()) {
    redirect('index.php');
}

$errors = [];
$form = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['full_name'] = trim((string) (filter_input(INPUT_POST, 'full_name', FILTER_DEFAULT) ?? ''));
    $form['email'] = trim((string) (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: ''));
    $form['phone'] = trim((string) (filter_input(INPUT_POST, 'phone', FILTER_DEFAULT) ?? ''));
    $password = (string) (filter_input(INPUT_POST, 'password', FILTER_DEFAULT) ?? '');
    $passwordConfirm = (string) (filter_input(INPUT_POST, 'password_confirm', FILTER_DEFAULT) ?? '');

    if ($form['full_name'] === '') {
        $errors[] = 'Full name is required.';
    }
    if ($form['email'] === '') {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $registered = $authService->register($form['full_name'], $form['email'], $password, $form['phone']);
        if (!$registered) {
            $errors[] = 'An account with this email already exists.';
        } else {
            Session::setFlash('success', 'Account created successfully.');
            redirect('index.php');
        }
    }
}

require __DIR__ . '/../app/Views/partials/header.php';
?>

<section class="page-head">
    <h1>Create Account</h1>
    <p>Sign up to track orders and checkout faster.</p>
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

<form class="checkout-form auth-form" method="post" action="<?= e(base_url('signup.php')) ?>">
    <label for="full_name">Full Name</label>
    <input id="full_name" name="full_name" type="text" value="<?= e($form['full_name']) ?>" required>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= e($form['email']) ?>" required>

    <label for="phone">Phone</label>
    <input id="phone" name="phone" type="text" value="<?= e($form['phone']) ?>">

    <label for="password">Password</label>
    <input id="password" name="password" type="password" minlength="6" required>

    <label for="password_confirm">Confirm Password</label>
    <input id="password_confirm" name="password_confirm" type="password" minlength="6" required>

    <button type="submit">Create account</button>
    <p class="auth-hint">Already have an account? <a href="<?= e(base_url('login.php')) ?>">Login here</a>.</p>
</form>

<?php require __DIR__ . '/../app/Views/partials/footer.php';
