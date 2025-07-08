<?php
require_once 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = :id");
        $stmt->execute([':id' => $existingItem['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);
    } 

    header('Location: shop.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id");
if ($stmt === false) {

} else {
    $stmt->execute([':user_id' => $user_id]);
}

$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gourd Shop</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?> to the gourd shop!</h1>

    <h2>Gourds</h2>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <?= $product['name']; ?> - <?= $product['price']; ?> SC
                <form method="POST" action="shop.php">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Your Cart</h2>
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
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
                    <th></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <p><a href="checkout.php">Proceed to Checkout</a></p>

    <p><a href="index.php">Click here to log out</a></p>

</body>
</html>