<?php
require_once 'db.php';
session_start();

if (isset($_SESSION['user_id']) === false) {
    header("Location: index.php");
    exit;
}


$stmt = $pdo->prepare("SELECT cart.id AS cart_id, products.name, products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

$stmt = $pdo->prepare("SELECT address FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$address = $user['address'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: payment.php");
    echo "<p>Thank you for your order, " . htmlspecialchars($_SESSION['username']) . "!</p>";
    exit;
}

//payment?
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <h2>Your Cart</h2>

    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty. Please <a href="shop.php">add some products</a> to your cart before proceeding.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= $item['name']; ?></td>
                        <td><?= $item['price']; ?> SC</td>
                        <td><?= $item['quantity']; ?></td>
                        <td><?= number_format($item['price'] * $item['quantity'], 2); ?> SC</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><?= number_format($totalPrice, 2); ?> SC</th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <h2>Place your order</h2>

    <form method="POST" action="checkout.php">   
        <label>Shipping Address: <textarea name="shipping_address" required><?= $user['address']; ?></textarea></label><br>     
        <button type="submit">Place Order</button>
    </form>

    <p><a href="shop.php">Back to Shop</a></p>
</body>
</html>
