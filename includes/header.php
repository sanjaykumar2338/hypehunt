<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

ensure_session();

$base = rtrim(BASE_URL, '/');
$adminBase = $base;
if (substr($base, -7) === '/public') {
    $adminBase = substr($base, 0, -7);
}
$assetBase = $adminBase . '/assets';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Hunt Admin</title>
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f5f5; }
        .navbar-brand { font-weight: 700; }
        .table-wrapper { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $adminBase; ?>/admin/dashboard.php">Hype Hunt Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $adminBase; ?>/admin/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $adminBase; ?>/admin/early_access.php">Early Access</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $adminBase; ?>/admin/notify.php">Notify Signups</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $adminBase; ?>/admin/blog/index.php">Blog Posts</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text me-3 fw-semibold text-white">
                        Hi <?php echo escape_html(ucfirst($_SESSION['admin_username'] ?? 'Admin')); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $adminBase; ?>/admin/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid mt-4">
