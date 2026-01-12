<?php
require_once __DIR__ . '/../config/db.php';

function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function sanitize_text(?string $value): string
{
    return trim((string) ($value ?? ''));
}

function sanitize_email(?string $value): string
{
    return filter_var(trim((string) ($value ?? '')), FILTER_SANITIZE_EMAIL) ?: '';
}

function allowed_user_type(?string $value): string
{
    $allowed = ['collector', 'reseller', 'both', 'unknown'];
    $value = strtolower(trim((string) ($value ?? '')));

    return in_array($value, $allowed, true) ? $value : 'unknown';
}

function get_client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            return trim($ipList[0]);
        }
    }

    return '0.0.0.0';
}

function respond_json(bool $success, string $message, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
    ]);
    exit;
}

function is_rate_limited(string $key, int $seconds = 2): bool
{
    ensure_session();
    $now = time();

    if (isset($_SESSION['rate_limit'][$key]) && ($now - $_SESSION['rate_limit'][$key]) < $seconds) {
        return true;
    }

    $_SESSION['rate_limit'][$key] = $now;

    return false;
}

function escape_html($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function get_admin_user(string $username): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);

    $user = $stmt->fetch();

    return $user ?: null;
}

function total_counts(): array
{
    $pdo = get_pdo();
    $counts = ['early_access' => 0, 'notify_signups' => 0];

    foreach ($counts as $table => &$count) {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM {$table}");
        $row = $stmt->fetch();
        $count = (int) ($row['total'] ?? 0);
    }

    return $counts;
}

function fetch_latest_early_access(int $limit = 10): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        'SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at
         FROM early_access
         ORDER BY created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_latest_notify_signups(int $limit = 10): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        'SELECT id, email, user_type, ip_address, created_at
         FROM notify_signups
         ORDER BY created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function count_early_access(string $search = ''): int
{
    $pdo = get_pdo();
    $search = trim($search);
    $where = '';

    if ($search !== '') {
        $where = 'WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search';
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM early_access {$where}");

    if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $row = $stmt->fetch();

    return (int) ($row['total'] ?? 0);
}

function fetch_early_access(string $search, int $limit, int $offset): array
{
    $pdo = get_pdo();
    $search = trim($search);
    $where = '';

    if ($search !== '') {
        $where = 'WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search';
    }

    $stmt = $pdo->prepare(
        "SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at
         FROM early_access
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function count_notify_signups(string $search = ''): int
{
    $pdo = get_pdo();
    $search = trim($search);
    $where = '';

    if ($search !== '') {
        $where = 'WHERE email LIKE :search OR user_type LIKE :search';
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notify_signups {$where}");

    if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $row = $stmt->fetch();

    return (int) ($row['total'] ?? 0);
}

function fetch_notify_signups(string $search, int $limit, int $offset): array
{
    $pdo = get_pdo();
    $search = trim($search);
    $where = '';

    if ($search !== '') {
        $where = 'WHERE email LIKE :search OR user_type LIKE :search';
    }

    $stmt = $pdo->prepare(
        "SELECT id, email, user_type, ip_address, created_at
         FROM notify_signups
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function valid_per_page(?int $perPage): int
{
    $allowed = [10, 25, 50];
    $perPage = $perPage ?: 10;

    return in_array($perPage, $allowed, true) ? $perPage : 10;
}

function current_page(?int $page): int
{
    $page = $page ?: 1;

    return $page > 0 ? $page : 1;
}

function export_rows(string $type): array
{
    $pdo = get_pdo();

    if ($type === 'early_access') {
        $stmt = $pdo->query(
            'SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at
             FROM early_access
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    if ($type === 'notify') {
        $stmt = $pdo->query(
            'SELECT id, email, user_type, ip_address, created_at
             FROM notify_signups
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    return [];
}
