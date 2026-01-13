<?php
require_once __DIR__ . '/../config/db.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        'SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at, confirm_email_sent, is_unsubscribed
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
        'SELECT id, email, user_type, ip_address, created_at, confirm_email_sent, is_unsubscribed
         FROM notify_signups
         ORDER BY created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function build_filters(string $search, ?string $flagColumn): array
{
    $whereParts = [];
    $params = [];

    if ($search !== '') {
        $whereParts[] = '(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if ($flagColumn === 'unsubscribed') {
        $whereParts[] = 'is_unsubscribed = 1';
    } elseif ($flagColumn === 'unsent') {
        $whereParts[] = 'confirm_email_sent = 0';
    }

    $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

    return [$where, $params];
}

function build_filters_notify(string $search, ?string $flagColumn): array
{
    $whereParts = [];
    $params = [];

    if ($search !== '') {
        $whereParts[] = '(email LIKE :search OR user_type LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if ($flagColumn === 'unsubscribed') {
        $whereParts[] = 'is_unsubscribed = 1';
    } elseif ($flagColumn === 'unsent') {
        $whereParts[] = 'confirm_email_sent = 0';
    }

    $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

    return [$where, $params];
}

function count_early_access(string $search = '', ?string $flagFilter = null): int
{
    $pdo = get_pdo();
    [$where, $params] = build_filters(trim($search), $flagFilter);

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM early_access {$where}");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch();

    return (int) ($row['total'] ?? 0);
}

function fetch_early_access(string $search, int $limit, int $offset, ?string $flagFilter = null): array
{
    $pdo = get_pdo();
    [$where, $params] = build_filters(trim($search), $flagFilter);

    $stmt = $pdo->prepare(
        "SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at,
                confirm_email_sent, is_unsubscribed, unsubscribed_at
         FROM early_access
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function count_notify_signups(string $search = '', ?string $flagFilter = null): int
{
    $pdo = get_pdo();
    [$where, $params] = build_filters_notify(trim($search), $flagFilter);

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notify_signups {$where}");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch();

    return (int) ($row['total'] ?? 0);
}

function fetch_notify_signups(string $search, int $limit, int $offset, ?string $flagFilter = null): array
{
    $pdo = get_pdo();
    [$where, $params] = build_filters_notify(trim($search), $flagFilter);

    $stmt = $pdo->prepare(
        "SELECT id, email, user_type, ip_address, created_at, confirm_email_sent, is_unsubscribed, unsubscribed_at
         FROM notify_signups
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
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
            'SELECT id, first_name, last_name, email, phone, comments, ip_address, created_at,
                    confirm_email_sent, is_unsubscribed, unsubscribed_at
             FROM early_access
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    if ($type === 'notify') {
        $stmt = $pdo->query(
            'SELECT id, email, user_type, ip_address, created_at, confirm_email_sent, is_unsubscribed, unsubscribed_at
             FROM notify_signups
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    return [];
}

function create_unsubscribe_token(string $email, string $listType): ?string
{
    $pdo = get_pdo();
    $attempts = 0;

    while ($attempts < 3) {
        $token = bin2hex(random_bytes(32));
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO unsubscribe_tokens (email, list_type, token) VALUES (:email, :list_type, :token)'
            );
            $stmt->execute([
                'email' => $email,
                'list_type' => $listType,
                'token' => $token,
            ]);

            return $token;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }
        }
        $attempts++;
    }

    return null;
}

function update_confirmation_status(string $table, string $email, bool $sent): void
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        "UPDATE {$table}
         SET confirm_email_sent = :sent,
             confirm_email_sent_at = CASE WHEN :sent = 1 THEN NOW() ELSE confirm_email_sent_at END
         WHERE email = :email"
    );
    $stmt->execute([
        'sent' => $sent ? 1 : 0,
        'email' => $email,
    ]);
}

function sendEmail(string $to, string $subject, string $htmlBody, string $toName = ''): bool
{
    if (!defined('SMTP_ENABLED') || SMTP_ENABLED === false) {
        // Local/dev: skip sending but report success so flows continue.
        return true;
    }

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host = SMTP_HOST;
        $mailer->SMTPAuth = true;
        $mailer->Username = SMTP_USER;
        $mailer->Password = SMTP_PASS;
        $mailer->SMTPSecure = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Port = SMTP_PORT;

        $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mailer->addAddress($to, $toName);
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $htmlBody;
        $mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

        $mailer->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail Error: ' . $mailer->ErrorInfo);
        return false;
    }
}

function send_confirmation_email(string $toEmail, string $toName, string $listType, string $unsubscribeUrl): bool
{
    $subject = $listType === 'early_access'
        ? 'HypeHunt Early Access Confirmed'
        : 'HypeHunt Launch List Confirmation';

    $copyName = trim($toName) !== '' ? $toName : 'there';

    $body = "<p>Thanks for joining the HypeHunt early access waitlist, {$copyName}! Your spot is officially secured—no further action needed right now.</p>"
        . '<p>Get ready for exclusive tools to hunt grails, track drops, and dominate the sneaker game. As an early member, you\'ll be first in line at launch.</p>'
        . '<p>We\'ll email you with access details and updates on hot releases as we get closer to launch.</p>'
        . '<p style="margin-top:20px; font-size:13px; color:#555;">If you no longer wish to hear from us, you can <a href="'
        . escape_html($unsubscribeUrl)
        . '">unsubscribe here</a>.<br>Contact: team@hypehunt.app</p>';

    return sendEmail($toEmail, $subject, $body, $toName);
}

function find_unsubscribe_token(string $token): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM unsubscribe_tokens WHERE token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function mark_unsubscribed(string $listType, string $email): void
{
    $pdo = get_pdo();

    if ($listType === 'early_access') {
        $stmt = $pdo->prepare(
            'UPDATE early_access
             SET is_unsubscribed = 1, unsubscribed_at = NOW()
             WHERE email = :email'
        );
    } else {
        $stmt = $pdo->prepare(
            'UPDATE notify_signups
             SET is_unsubscribed = 1, unsubscribed_at = NOW()
             WHERE email = :email'
        );
    }

    $stmt->execute(['email' => $email]);
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: bin2hex(random_bytes(4));
}

function unique_slug(string $baseSlug, ?int $excludeId = null): string
{
    $pdo = get_pdo();
    $slug = $baseSlug ?: bin2hex(random_bytes(4));
    $counter = 1;
    while (true) {
        $sql = 'SELECT id FROM blog_posts WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $excludeId;
        }
        $stmt = $pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
}

function generate_csrf_token(): string
{
    ensure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    ensure_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function sanitize_blog_content(string $html): string
{
    $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    $allowed = '<p><br><br/><h1><h2><h3><h4><ul><ol><li><strong><em><b><i><a><img>';
    $clean = strip_tags($html, $allowed);
    $clean = preg_replace('/<a(?![^>]*rel=)/i', '<a rel="noopener noreferrer"', $clean);
    return $clean;
}

function count_blog_posts(string $search = '', ?string $status = null): int
{
    $pdo = get_pdo();
    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = 'title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    if (in_array($status, ['draft', 'published'], true)) {
        $where[] = 'status = :status';
        $params[':status'] = $status;
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM blog_posts {$whereSql}");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch();
    return (int) ($row['total'] ?? 0);
}

function fetch_blog_posts(string $search, ?string $status, int $limit, int $offset): array
{
    $pdo = get_pdo();
    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = 'title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    if (in_array($status, ['draft', 'published'], true)) {
        $where[] = 'status = :status';
        $params[':status'] = $status;
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare(
        "SELECT * FROM blog_posts
         {$whereSql}
         ORDER BY COALESCE(published_at, created_at) DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_blog_post_by_id(int $id): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function get_blog_post_by_slug(string $slug, bool $allowDraft = false): ?array
{
    $pdo = get_pdo();
    if ($allowDraft) {
        $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = :slug AND status = "published" LIMIT 1');
        $stmt->execute(['slug' => $slug]);
    }
    $row = $stmt->fetch();
    return $row ?: null;
}
