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
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        respond_json(false, 'This email is already registered.', 409);
    }

    respond_json(false, 'Unable to save your request right now.', 500);
}

// Keep token/email attempts from breaking a successful signup
$token = null;
try {
    $token = create_unsubscribe_token($email, 'early_access');
} catch (Throwable $e) {
    error_log('Early access token error: ' . $e->getMessage());
}

if ($token !== null) {
    try {
        $unsubscribeUrl = rtrim(BASE_URL, '/') . '/unsubscribe.php?token=' . urlencode($token);
        $emailSent = send_confirmation_email($email, "{$firstName} {$lastName}", 'early_access', $unsubscribeUrl);
        update_confirmation_status('early_access', $email, $emailSent);
    } catch (Throwable $e) {
        error_log('Early access email/update error: ' . $e->getMessage());
    }
}

$msg = "Thanks for joining the HypeHunt early access waitlist, {$firstName}! Your spot is officially secured—no further action needed right now.";
respond_json(true, $msg);
