<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $address = $_POST['address'];

    $result = registerUser($username, $password, $address);

    if ($result === true) {
        echo "User registered successfully!";
        header('Location: index.php');
        exit;
    } else {
        echo $result;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h1>Sign Up</h1>
    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" id="password" required></label><br>
        <label>Address: <textarea name="address" required></textarea></label><br>
        <button type="submit">Register</button>
        <p id="password-strength"></p>
    </form>
    <script>
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthMessage = document.getElementById('password-strength');

            const minLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSpecialChar = /[\W_]/.test(password);

            if (minLength && hasUppercase && hasLowercase && hasNumber && hasSpecialChar) {
                strengthMessage.textContent = 'Password is strong.';
                strengthMessage.style.color = 'green';
            } else {
                strengthMessage.textContent = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
                strengthMessage.style.color = 'red';
            }
        });
    </script>
</body>
</html>
