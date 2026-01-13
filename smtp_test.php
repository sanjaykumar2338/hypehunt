<?php
// Simple SMTP smoke test. Usage:
//   SMTP_TEST_TO=you@example.com FORCE_SMTP=1 php smtp_test.php
// or: FORCE_SMTP=1 php smtp_test.php you@example.com

require_once __DIR__ . '/config/config.php';

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$toEmail = getenv('SMTP_TEST_TO') ?: ($argv[1] ?? 'sk963070@gmail.com');
$subject = 'HypeHunt SMTP Test';
$body = '<p>This is a test email from HypeHunt SMTP test script.</p><p>Timestamp: ' . date('c') . '</p>';

$host = defined('SMTP_HOST') ? SMTP_HOST : 'mail.hypehunt.app';
$port = defined('SMTP_PORT') ? SMTP_PORT : 465;
$user = defined('SMTP_USER') ? SMTP_USER : 'team@hypehunt.app';
$pass = defined('SMTP_PASS') ? SMTP_PASS : 'x0ueY{zDS/o78_O@';
$fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'team@hypehunt.app';
$fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'HypeHunt';
$encryption = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'ssl';

$mailer = new PHPMailer(true);

try {
    $mailer->isSMTP();
    $mailer->Host = $host;
    $mailer->SMTPAuth = true;
    $mailer->Username = $user;
    $mailer->Password = $pass;
    $mailer->SMTPSecure = $encryption;
    $mailer->Port = $port;

    $mailer->setFrom($fromEmail, $fromName);
    $mailer->addAddress($toEmail);
    $mailer->isHTML(true);
    $mailer->Subject = $subject;
    $mailer->Body = $body;
    $mailer->AltBody = strip_tags($body);

    $mailer->send();
    echo "SMTP test sent to {$toEmail}\n";
} catch (Exception $e) {
    fwrite(STDERR, "SMTP test failed: " . $mailer->ErrorInfo . "\n");
    exit(1);
}
