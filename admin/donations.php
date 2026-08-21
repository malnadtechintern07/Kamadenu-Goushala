<?php
require_once __DIR__ . '/header.php';

$donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-donate text-warning me-2"></i> Verified Donations & Receipts</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donor Name</th>
                    <th>Email / Phone</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th>Receipt #</th>
                    <th>Payment ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $d): ?>
                    <tr>
                        <td class="font-mono small"><?php echo date('Y-m-d H:i', strtotime($d['created_at'])); ?></td>
                        <td><strong><?php echo e($d['donor_name']); ?></strong></td>
                        <td class="small"><?php echo e($d['donor_email']); ?><br><?php echo e($d['donor_phone']); ?></td>
                        <td><?php echo e($d['purpose']); ?></td>
                        <td class="font-mono fw-bold text-success">₹<?php echo number_format($d['amount'], 2); ?></td>
                        <td><span class="badge bg-dark font-mono"><?php echo e($d['receipt_number']); ?></span></td>
                        <td><small class="font-mono text-muted"><?php echo e($d['payment_id']); ?></small></td>
                        <td><span class="badge bg-success"><?php echo e($d['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
