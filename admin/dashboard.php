<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$counts = total_counts();
$latestEarly = fetch_latest_early_access();
$latestNotify = fetch_latest_notify_signups();
?>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0">
            <div class="card-body">
                <h5 class="card-title">Early Access Signups</h5>
                <p class="display-6 mb-0"><?php echo escape_html($counts['early_access']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0">
            <div class="card-body">
                <h5 class="card-title">Notify Signups</h5>
                <p class="display-6 mb-0"><?php echo escape_html($counts['notify_signups']); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Latest Early Access</h5>
                <a href="<?php echo $adminBase; ?>/admin/early_access.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestEarly)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No records yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestEarly as $row): ?>
                                <tr>
                                    <td><?php echo escape_html($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo escape_html($row['email']); ?></td>
                                    <td><?php echo escape_html($row['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="table-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Latest Notify Signups</h5>
                <a href="<?php echo $adminBase; ?>/admin/notify.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Email</th>
                            <th scope="col">Type</th>
                            <th scope="col">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestNotify)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No records yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestNotify as $row): ?>
                                <tr>
                                    <td><?php echo escape_html($row['email']); ?></td>
                                    <td><?php echo escape_html($row['user_type']); ?></td>
                                    <td><?php echo escape_html($row['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
