<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php?redirect=" . urlencode('/Kamadenu/my-orders.php') . "&msg=login_required"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Product Store Orders</h1>
        <p class="text-white-50 mb-0">Track delivery status for your Goushala store purchases.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="kamadenu-card p-4">
            <?php if (empty($orders)): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-3">You have not placed any store orders yet.</p>
                    <a href="/Kamadenu/products.php" class="btn btn-warning font-ui fw-bold px-4">Browse Store</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order Code</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Payment</th>
                                <th>Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><span class="badge bg-secondary font-mono"><?php echo e($o['order_code']); ?></span></td>
                                    <td class="font-mono small"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                    <td class="font-mono fw-bold">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td>
                                        <?php if ($o['payment_status'] === 'Paid' || $o['payment_status'] === 'Completed'): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Payment Complete</span>
                                        <?php elseif ($o['payment_status'] === 'Pending Approval' || $o['payment_status'] === 'Pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> <?php echo e($o['payment_status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-warning text-dark font-ui fw-bold"><?php echo e($o['order_status']); ?></span></td>
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
