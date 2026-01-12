<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ensure_session();
session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/admin/login.php');
exit;
