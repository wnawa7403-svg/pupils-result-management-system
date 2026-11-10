<?php
session_start();
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== "Admin") {
    header("Location: ../Login/login.php");
    exit();
}

require_once "../connection/connection.php";

// Fetch users
$users_result = $con->query("SELECT user_id, account, failed_attempts, last_failed FROM login ORDER BY user_id ASC");

// Summary counts
$active = $inactive = $locked = 0;
if ($users_result && $users_result->num_rows > 0) {
    while ($user = $users_result->fetch_assoc()) {
        if ($user['account'] === "Activate") $active++;
        else if ($user['failed_attempts'] >= 4) $locked++;
        else $inactive++;
    }
    $users_result->data_seek(0); // Reset for table
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Accounts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.status-active { color: green; font-weight: bold; }
.status-inactive { color: orange; font-weight: bold; }
.status-locked { color: red; font-weight: bold; }
</style>
</head>
<body class="p-3">

<h3>All User Accounts</h3>

<div class="mb-3">
    <span class="me-3">Active: <?= $active ?></span>
    <span class="me-3">Inactive: <?= $inactive ?></span>
    <span class="me-3">Locked: <?= $locked ?></span>
</div>

<div class="mb-3">
    <button class="btn btn-sm btn-primary filter-btn" data-filter="all">All</button>
    <button class="btn btn-sm btn-success filter-btn" data-filter="Activate">Active</button>
    <button class="btn btn-sm btn-warning filter-btn" data-filter="Deactivate">Inactive</button>
    <button class="btn btn-sm btn-danger filter-btn" data-filter="locked">Locked</button>
</div>

<table class="table table-striped" id="accounts-table">
<thead>
<tr>
    <th>User ID</th>
    <th>Status</th>
    <th>Failed Attempts</th>
    <th>Last Failed</th>
</tr>
</thead>
<tbody>
<?php if ($users_result && $users_result->num_rows > 0): ?>
    <?php while ($user = $users_result->fetch_assoc()): 
        $is_locked = $user['failed_attempts'] >= 4 && $user['account'] === "Deactivate";
        $status_text = $user['account'] === "Activate" ? "Active" : ($is_locked ? "Locked" : "Inactive");
        $status_class = $user['account'] === "Activate" ? "status-active" : ($is_locked ? "status-locked" : "status-inactive");
        $last_failed = !empty($user['last_failed']) && $user['last_failed'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i', strtotime($user['last_failed'])) : '-';
    ?>
    <tr class="account-row" data-status="<?= $is_locked ? 'locked' : $user['account']; ?>">
        <td><?= htmlspecialchars($user['user_id']); ?></td>
        <td class="<?= $status_class ?>"><?= $status_text ?></td>
        <td><?= $user['failed_attempts'] ?></td>
        <td><?= $last_failed ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr><td colspan="4">No users found.</td></tr>
<?php endif; ?>
</tbody>
</table>

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const filter = btn.dataset.filter;
        document.querySelectorAll('.account-row').forEach(row => {
            row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
        });
    });
});
</script>

</body>
</html>