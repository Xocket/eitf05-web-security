<?php 
require_once '../backend/db.php';
session_start();

if (isset($_SESSION['user_id']) === false) {
    header("Location: login.php");
    exit;
}

$wallet_address = 'hbsajfbwuyer723134msajdfv';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT cart.id AS cart_id, products.name, products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);

$items_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($items_in_cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
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
        <h1>SEND <?= $totalPrice; ?> COIN TO <?= $wallet_address; ?> </h1>
        <label>
            Transaction ID: 
            <input type="text">
        </label>
    </main>
</body>
</html>