<?php
require_once __DIR__ . '/header.php';

if (isset($_POST['vol_id']) && isset($_POST['action'])) {
    $vol_id = intval($_POST['vol_id']);
    $new_status = $_POST['action'] === 'approve' ? 'Approved' : 'Rejected';
    $pdo->prepare("UPDATE volunteers SET status = ? WHERE id = ?")->execute([$new_status, $vol_id]);
    log_audit($pdo, "Volunteer Application {$new_status}", 'volunteers', $vol_id);
    header("Location: /Kamadenu/admin/volunteers.php");
    exit;
}

$volunteers = $pdo->query("SELECT * FROM volunteers ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-hands-helping text-warning me-2"></i> Volunteer Applications Management</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Applied Date</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Availability</th>
                    <th>Interest Area</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($volunteers as $v): ?>
                    <tr>
                        <td class="font-mono small"><?php echo date('Y-m-d', strtotime($v['applied_at'])); ?></td>
                        <td><strong><?php echo e($v['name']); ?></strong></td>
                        <td class="small"><?php echo e($v['email']); ?><br><?php echo e($v['phone']); ?></td>
                        <td><?php echo e($v['availability']); ?></td>
                        <td><span class="badge bg-warning-subtle text-dark"><?php echo e($v['interest_area']); ?></span></td>
                        <td>
                            <span class="badge <?php echo $v['status'] === 'Approved' ? 'bg-success' : ($v['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                                <?php echo e($v['status']); ?>

                            </span>
                        </td>
                        <td>
                            <?php if ($v['status'] === 'Pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="vol_id" value="<?php echo $v['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-success font-ui">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger font-ui">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted me-2">Reviewed</span>
                            <?php endif; ?>
                            <button onclick="deleteAdminItem('volunteers', <?php echo $v['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
