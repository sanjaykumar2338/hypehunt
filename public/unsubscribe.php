<?php
require_once __DIR__ . '/../includes/functions.php';

$token = sanitize_text($_GET['token'] ?? '');
$appName = 'HypeHunt';
$message = 'Invalid or expired link.';

if ($token !== '') {
    $row = find_unsubscribe_token($token);

    if ($row) {
        $listType = $row['list_type'];
        $email = $row['email'];
        mark_unsubscribed($listType, $email);
        $message = "You've been unsubscribed successfully from {$appName}.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape_html($appName); ?> | Unsubscribe</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f172a; color: #e5e7eb; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #111827; padding: 28px 32px; border-radius: 12px; max-width: 420px; width: 90%; box-shadow: 0 12px 40px rgba(0,0,0,0.35); }
        h1 { margin: 0 0 12px; font-size: 22px; }
        p { margin: 0; line-height: 1.5; color: #cbd5e1; }
        a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1><?php echo escape_html($appName); ?></h1>
        <p><?php echo escape_html($message); ?></p>
    </div>
</body>
</html>
