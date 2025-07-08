<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        header('Location: shop.php');
        exit;
    } else {
        echo htmlspecialchars("Invalid username or password.\nIf you've entered the incorrect password more than 5 times contact support.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
    <p>If you don't have an account, <a href="signup.php">click here to sign up</a>.</p>
</body>
</html>
