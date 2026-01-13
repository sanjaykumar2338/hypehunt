<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$type = $_GET['type'] ?? '';

if (!in_array($type, ['early_access', 'notify'], true)) {
    http_response_code(400);
    echo 'Invalid export type.';
    exit;
}

$rows = export_rows($type);
$filename = $type . '_export_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

if ($type === 'early_access') {
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Comments', 'IP Address', 'Confirm Email Sent', 'Unsubscribed', 'Unsubscribed At', 'Created At']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['comments'],
            $row['ip_address'],
            $row['confirm_email_sent'],
            $row['is_unsubscribed'],
            $row['unsubscribed_at'],
            $row['created_at'],
        ]);
    }
} else {
    fputcsv($output, ['ID', 'Email', 'User Type', 'IP Address', 'Confirm Email Sent', 'Unsubscribed', 'Unsubscribed At', 'Created At']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row['id'],
            $row['email'],
            $row['user_type'],
            $row['ip_address'],
            $row['confirm_email_sent'],
            $row['is_unsubscribed'],
            $row['unsubscribed_at'],
            $row['created_at'],
        ]);
    }
}

fclose($output);
exit;
