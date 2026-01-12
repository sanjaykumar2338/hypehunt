<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(false, 'Invalid request method.', 405);
}

if (is_rate_limited('notify_signup')) {
    respond_json(false, 'Please wait a moment before trying again.', 429);
}

$email = sanitize_email($_POST['email'] ?? '');
$userType = allowed_user_type($_POST['user_type'] ?? '');
$ipAddress = get_client_ip();

if ($email === '') {
    respond_json(false, 'Email is required.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond_json(false, 'Please enter a valid email.', 400);
}

try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        'INSERT INTO notify_signups (email, user_type, ip_address)
         VALUES (:email, :user_type, :ip)'
    );

    $stmt->execute([
        'email' => $email,
        'user_type' => $userType,
        'ip' => $ipAddress,
    ]);

    respond_json(true, "Thanks! You're on the launch list.");
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        respond_json(false, 'This email is already registered.', 409);
    }

    respond_json(false, 'Unable to save your signup right now.', 500);
}
