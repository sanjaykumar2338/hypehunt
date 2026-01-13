<?php
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$id = (int) ($_POST['id'] ?? 0);
$token = $_POST['csrf_token'] ?? '';

if (!verify_csrf_token($token)) {
    http_response_code(400);
    exit('Invalid CSRF token');
}

$base = rtrim(BASE_URL, '/');
if (substr($base, -7) === '/public') {
    $base = substr($base, 0, -7);
}

if ($id > 0) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM blog_posts WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
}

header('Location: ' . $base . '/admin/blog/index.php');
exit;
