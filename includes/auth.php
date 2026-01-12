<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

ensure_session();

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}
