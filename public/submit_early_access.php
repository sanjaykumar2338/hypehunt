<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(false, 'Invalid request method.', 405);
}

if (is_rate_limited('early_access')) {
    respond_json(false, 'Please wait a moment before trying again.', 429);
}

$firstName = substr(sanitize_text($_POST['first_name'] ?? ''), 0, 80);
$lastName = substr(sanitize_text($_POST['last_name'] ?? ''), 0, 80);
$email = sanitize_email($_POST['email'] ?? '');
$phone = substr(sanitize_text($_POST['phone'] ?? ''), 0, 30);
$password = (string) ($_POST['password'] ?? '');
$comments = substr(sanitize_text($_POST['comments'] ?? ''), 0, 1000);
$ipAddress = get_client_ip();

if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
    respond_json(false, 'Please complete all required fields.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond_json(false, 'Please enter a valid email.', 400);
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        'INSERT INTO early_access (first_name, last_name, email, phone, password_hash, comments, ip_address)
         VALUES (:first_name, :last_name, :email, :phone, :password_hash, :comments, :ip)'
    );

    $stmt->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone ?: null,
        'password_hash' => $passwordHash,
        'comments' => $comments ?: null,
        'ip' => $ipAddress,
    ]);

    respond_json(true, "Thanks! You're on the early access list.");
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        respond_json(false, 'This email is already registered.', 409);
    }

    respond_json(false, 'Unable to save your request right now.', 500);
}
