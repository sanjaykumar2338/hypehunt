<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ensure_session();

$base = rtrim(BASE_URL, '/');
$adminBase = $base;
if (substr($base, -7) === '/public') {
    $adminBase = substr($base, 0, -7);
}
$assetBase = $adminBase . '/assets';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . $adminBase . '/admin/dashboard.php');
    exit;
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_text($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } elseif (is_rate_limited('admin_login')) {
        $error = 'Please wait a moment before trying again.';
    } else {
        $user = get_admin_user($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header('Location: ' . $adminBase . '/admin/dashboard.php');
            exit;
        }

        $error = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/bootstrap.min.css">
    <style>
        body { background: #0f172a; color: #fff; }
        .login-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #111827; padding: 36px; border-radius: 18px; width: 100%; max-width: 460px; box-shadow: 0 20px 60px rgba(0,0,0,0.35); }
        .form-control { background: #0b1220; border-color: #1f2937; color: #fff; }
        .form-control:focus { background: #0b1220; color: #fff; box-shadow: 0 0 0 0.25rem rgba(59,130,246,0.2); }
        .btn-primary { background: #3DFF8E; border-color: #3DFF8E; color: #0f172a; font-weight: 700; }
        .btn-primary:hover { background: #2ed676; border-color: #2ed676; color: #0f172a; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h3 class="mb-4 text-center">Hype Hunt Admin</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo escape_html($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
    <script src="<?php echo $assetBase; ?>/js/bootstrap.bundle.min.js"></script>
</body>
</html>
