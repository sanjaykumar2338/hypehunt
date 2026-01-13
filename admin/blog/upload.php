<?php
require_once __DIR__ . '/../../includes/auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

if (empty($_FILES['file']['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error']);
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

$slug = sanitize_text($_POST['slug'] ?? 'upload');
$safeSlug = preg_replace('/[^a-z0-9-]+/i', '-', $slug);
$uniqueName = time() . '_' . $safeSlug . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
$targetDir = __DIR__ . '/../../assets/uploads/blog/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0775, true);
}
$targetPath = $targetDir . $uniqueName;
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}

$url = 'assets/uploads/blog/' . $uniqueName;
echo json_encode(['success' => true, 'url' => $url]);
exit;
