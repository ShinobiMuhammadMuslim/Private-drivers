<?php

// Database connection
function db_connect() {
    $host = 'localhost';
    $db   = 'your_database';
    $user = 'your_username';
    $pass = 'your_password';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\Exception $e) {
        throw new unTimeException($e->getMessage());
    }
}

// User registration
function register_user($email, $password) {
    $db = db_connect();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    return $stmt->execute([$email, $hash]);
}

// User login
function login_user($email, $password) {
    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return true;
    }
    return false;
}

// Password reset
function reset_password($email) {
    $db = db_connect();
    $temp_password = bin2hex(random_bytes(8));
    $hash = password_hash($temp_password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    //send temporary password via email/SMS
    send_notification($email, 'Your temporary password is: ' . $temp_password);
}

// Notification handling
function send_notification($to, $message) {
    // Implement email/SMS sending logic here
}

// User management functions
function get_user($email) {
    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

?>