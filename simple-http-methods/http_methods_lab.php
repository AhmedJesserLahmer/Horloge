<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$method = effectiveMethod();
$response = [
    'method' => $method,
    'message' => '',
    'tip' => 'Use _method=PUT or _method=DELETE in forms to simulate methods in browser.',
];

if ($method === 'GET') {
    $response['message'] = 'GET: used to READ data.';
} elseif ($method === 'POST') {
    $response['message'] = 'POST: used to CREATE data.';
    $response['posted_name'] = (string) ($_POST['name'] ?? '');
} elseif ($method === 'PUT') {
    $response['message'] = 'PUT: used to UPDATE data.';
    $response['updated_id'] = (int) ($_POST['id'] ?? 0);
} elseif ($method === 'DELETE') {
    $response['message'] = 'DELETE: used to REMOVE data.';
    $response['deleted_id'] = (int) ($_POST['id'] ?? 0);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTTP Methods Lab</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <a href="index.php">&larr; Home</a>
        <h1>HTTP Methods Lab</h1>
        <p class="badge">Current method: <?= h($method) ?></p>

        <div class="card">
            <h3>Server response</h3>
            <pre><?= h(json_encode($response, JSON_PRETTY_PRINT)) ?></pre>
        </div>

        <div class="card">
            <h3>GET example</h3>
            <form method="get" action="http_methods_lab.php">
                <button type="submit">Run GET</button>
            </form>
        </div>

        <div class="card">
            <h3>POST example</h3>
            <form method="post" action="http_methods_lab.php">
                <input type="text" name="name" placeholder="Name for POST">
                <button type="submit">Run POST</button>
            </form>
        </div>

        <div class="card">
            <h3>PUT example (simulated)</h3>
            <form method="post" action="http_methods_lab.php">
                <input type="hidden" name="_method" value="PUT">
                <input type="number" name="id" value="1" min="1">
                <button type="submit">Run PUT</button>
            </form>
        </div>

        <div class="card">
            <h3>DELETE example (simulated)</h3>
            <form method="post" action="http_methods_lab.php">
                <input type="hidden" name="_method" value="DELETE">
                <input type="number" name="id" value="1" min="1">
                <button type="submit">Run DELETE</button>
            </form>
        </div>
    </div>
</body>

</html>