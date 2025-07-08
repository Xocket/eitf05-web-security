<?php 
require_once 'db.php';
require_once 'auth.php';
session_start();

if (isset($_SESSION['user_id']) === false) {
    header("Location: index.php");
    exit;
}

$wallet_address = getenv('WEBSHOP_ADDRESS');
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT cart.id AS cart_id, products.name, products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);

$items_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($items_in_cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = sanitize($_POST['transaction_id']);

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, transaction_id, transaction_total) VALUES (:user_id, :transaction_id, :transaction_total)");
    $stmt->execute([':user_id' => $user_id, ':transaction_id' => $product_id, ':transaction_total' => $totalPrice]);

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    header('Location: receipt.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <h1>Confirm your purchase by sending <?= htmlspecialchars($totalPrice); ?> SC to <?= htmlspecialchars($wallet_address); ?> </h1>
        <form method="POST">
            <label for="transaction_id">Enter the transaction ID: </label>
            <input name="transaction_id" id="transaction_id">
            <button type="submit">Purchase</button>
        </form>
    </main>
</body>
</html>