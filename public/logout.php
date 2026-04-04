<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../bootstrap/app.php';

$authService->logout();
Session::setFlash('success', 'You have been logged out.');
redirect('index.php');
