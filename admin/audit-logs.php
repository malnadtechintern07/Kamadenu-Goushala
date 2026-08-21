<?php
require_once __DIR__ . '/header.php';

$logs = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 50")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-history text-warning me-2"></i> System Audit Logs</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle small font-mono">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Admin User</th>
                    <th>Action Executed</th>
                    <th>Target Table</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?php echo e($l['created_at']); ?></td>
                        <td><strong><?php echo e($l['admin_name']); ?></strong></td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning"><?php echo e($l['action']); ?></span></td>
                        <td><?php echo e($l['target_table']); ?></td>
                        <td>#<?php echo $l['record_id']; ?></td>
                        <td><?php echo e($l['ip_address']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
