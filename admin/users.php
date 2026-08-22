<?php
require_once __DIR__ . '/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-users text-warning me-2"></i> Registered Devotees & Users</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gouseva Points</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="font-mono">#U-<?php echo $u['id']; ?></td>
                        <td><strong><?php echo e($u['name']); ?></strong></td>
                        <td><?php echo e($u['email']); ?></td>
                        <td><?php echo e($u['phone']); ?></td>
                        <td><span class="badge bg-warning text-dark font-mono"><?php echo $u['gouseva_points']; ?> pts</span></td>
                        <td><span class="badge bg-success"><?php echo e($u['status']); ?></span></td>
                        <td class="font-mono small"><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                        <td>
                            <button onclick="deleteAdminItem('users', <?php echo $u['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
