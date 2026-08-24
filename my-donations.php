<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$donations = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Donation History</h1>
        <p class="text-white-50 mb-0">Your past devotional contributions and 80G tax exemption receipts.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="kamadenu-card p-4">
            <?php if (empty($donations)): ?>
                <p class="text-muted text-center py-4">No donation history recorded yet.</p>
            <?php else: ?> 
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Purpose</th>
                                <th>Amount</th>
                                <th>Receipt #</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <  tbody>
                            <?php foreach ($donations as $d): ?>
                                <tr>
                                    <td class="font-mono small"><?php echo date('M d, Y H:i', strtotime($d['created_at'])); ?></td>
                                    <td><strong><?php echo e($d['purpose']); ?></strong></td>
                                    <td class="font-mono fw-bold text-success">₹<?php echo number_format($d['amount'], 2); ?></td>
                                    <td><span class="badge bg-dark font-mono"><?php echo e($d['receipt_number']); ?></span></td>
                                    <td><span class="badge bg-success"><?php echo e($d['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
