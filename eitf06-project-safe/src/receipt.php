<?php
require_once 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Your receipts: </h1>

        <?php foreach ($orders as $order): ?> 
            <h2>Transaction ID: <?= htmlspecialchars($order['transaction_id']) ?></h2>
            <h3>Transaction Total: <?= htmlspecialchars($order['transaction_total']) ?></h3>
        <?php endforeach ?>

        <p><a href="shop.php">Click here to continue shopping</a></p>
        <p><a href="index.php">Click here to log out</a></p>
    </main>
</body>
</html>