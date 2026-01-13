<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$search = sanitize_text($_GET['search'] ?? '');
$perPage = valid_per_page(isset($_GET['per_page']) ? (int) $_GET['per_page'] : 10);
$page = current_page(isset($_GET['page']) ? (int) $_GET['page'] : 1);
$flag = in_array(($_GET['filter'] ?? ''), ['unsubscribed', 'unsent'], true) ? $_GET['filter'] : null;

$total = count_notify_signups($search, $flag);
$totalPages = max(1, (int) ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$records = fetch_notify_signups($search, $perPage, $offset, $flag);

$queryBase = http_build_query([
    'search' => $search,
    'per_page' => $perPage,
    'filter' => $flag,
]);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Notify Signups</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/admin/export.php?type=notify">Export CSV</a>
    </div>
</div>

<form class="row g-3 mb-3" method="GET" action="">
    <div class="col-md-6 col-lg-4">
        <label for="search" class="form-label">Search (email or type)</label>
        <input type="text" id="search" name="search" value="<?php echo escape_html($search); ?>" class="form-control" placeholder="Search records">
    </div>
    <div class="col-md-3 col-lg-2">
        <label for="per_page" class="form-label">Per page</label>
        <select id="per_page" name="per_page" class="form-select">
            <?php foreach ([10, 25, 50] as $option): ?>
                <option value="<?php echo $option; ?>" <?php echo $perPage === $option ? 'selected' : ''; ?>>
                    <?php echo $option; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 col-lg-2">
        <label for="filter" class="form-label">Filter</label>
        <select id="filter" name="filter" class="form-select">
            <option value="" <?php echo $flag === null ? 'selected' : ''; ?>>All</option>
            <option value="unsent" <?php echo $flag === 'unsent' ? 'selected' : ''; ?>>Confirm email not sent</option>
            <option value="unsubscribed" <?php echo $flag === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
        </select>
    </div>
    <div class="col-md-3 col-lg-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
</form>

<div class="table-wrapper">
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Email</th>
                    <th scope="col">User Type</th>
                    <th scope="col">IP</th>
                    <th scope="col">Confirm Email Sent</th>
                    <th scope="col">Unsubscribed</th>
                    <th scope="col">Unsubscribed At</th>
                    <th scope="col">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?php echo escape_html($row['id']); ?></td>
                            <td><?php echo escape_html($row['email']); ?></td>
                            <td><?php echo escape_html($row['user_type']); ?></td>
                            <td><?php echo escape_html($row['ip_address']); ?></td>
                            <td><?php echo $row['confirm_email_sent'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $row['is_unsubscribed'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo escape_html($row['unsubscribed_at']); ?></td>
                            <td><?php echo escape_html($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Pagination">
            <ul class="pagination">
                <?php
                $prevPage = max(1, $page - 1);
                $nextPage = min($totalPages, $page + 1);
                ?>
                <li class="page-item <?php echo $page === 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryBase . '&page=' . $prevPage; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo $queryBase . '&page=' . $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page === $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryBase . '&page=' . $nextPage; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
