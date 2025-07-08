<?php
require_once 'db.php';

function isBlacklisted($password){
    $blacklist = __DIR__ . '/10k-most-common.txt';
    $passwords = file($blacklist);
    return in_array(strtolower($password), $passwords);
}

function sanitize($text) {
    $text = trim($text);
    $text = stripslashes($text);
    return htmlspecialchars($text);
}

function registerUser($username, $password, $address) {
    global $pdo;

    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/\d/', $password) ||
        !preg_match('/[\W_]/', $password)) {
        return "Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.";
    }

    if (isBlacklisted($password)) {
        return "This password is too common. Please choose another one.";
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, address) VALUES (:username, :password_hash, :address)");

    $stmt->execute([
        ':username' => sanitize($username),
        ':password_hash' => sanitize($hashedPassword),
        ':address' => sanitize($address)
    ]);

    return true;
}

function loginUser($username, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => sanitize($username)]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['login_attempts'] < 5 && password_verify(sanitize($password), $user['password_hash'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    } elseif ($user) {
        $stmt = $pdo->prepare("UPDATE users SET login_attempts = :login_attempts WHERE id = :user_id");
        $stmt->execute([':login_attempts' => $user['login_attempts'] + 1, ':user_id' => $user['id']]);
    }

    return false;
}
?>
