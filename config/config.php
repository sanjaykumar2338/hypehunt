<?php
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
$forceProd = getenv('FORCE_PROD_ENV') === '1' || getenv('FORCE_SMTP') === '1';
$isLocal = !$forceProd && (
    (PHP_SAPI === 'cli')
    || ($httpHost === 'localhost')
    || (strpos($httpHost, 'localhost') !== false)
    || ($serverAddr === '127.0.0.1')
);

if ($isLocal) {
    // =========================
    // LOCAL ENVIRONMENT
    // =========================
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'hypehunt_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    define('BASE_URL', 'http://localhost/myworkplace/upwork/hypehunt/public');

    // Enable SMTP locally using the production creds so form tests actually send mail.
    define('SMTP_ENABLED', true);
    define('SMTP_HOST', getenv('SMTP_HOST') ?: 'mail.hypehunt.app');
    define('SMTP_PORT', getenv('SMTP_PORT') ?: 465);
    define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'ssl');
    define('SMTP_USER', getenv('SMTP_USER') ?: 'team@hypehunt.app');
    define('SMTP_PASS', getenv('SMTP_PASS') ?: 'x0ueY{zDS/o78_O@');
    define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'team@hypehunt.app');
    define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'HypeHunt');
} else {
    // =========================
    // PRODUCTION ENVIRONMENT
    // =========================
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'hypehunt_db');
    define('DB_USER', 'hypehunt_db');
    define('DB_PASS', 'FDKrlGt9tk@V8|Us');

    define('BASE_URL', 'https://hypehunt.app');

    // SMTP ENABLED (Production)
    define('SMTP_ENABLED', true);

    define('SMTP_HOST', 'mail.hypehunt.app');
    define('SMTP_PORT', 465);
    define('SMTP_ENCRYPTION', 'ssl'); // SSL/TLS
    define('SMTP_USER', 'team@hypehunt.app');
    define('SMTP_PASS', 'x0ueY{zDS/o78_O@');
    define('SMTP_FROM_EMAIL', 'team@hypehunt.app');
    define('SMTP_FROM_NAME', 'HypeHunt');
}
