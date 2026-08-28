<?php
require_once __DIR__ . '/includes/header.php';

$payment_id = isset($_GET['payment_id']) ? trim($_GET['payment_id']) : '';
$receipt_number = isset($_GET['receipt']) ? trim($_GET['receipt']) : '';
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;

$payment = null;
if (!empty($payment_id)) {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_id = ? OR order_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$payment_id, $payment_id]);
    $payment = $stmt->fetch();
}

$status = 'Pending Approval';
if ($payment) {
    $status = $payment['status'];
} elseif (isset($_GET['status'])) {
    $status = $_GET['status'];
}

$is_completed = ($status === 'Captured' || $status === 'Completed' || $status === 'Paid');
$is_failed = ($status === 'Failed' || $status === 'Payment Failed' || $status === 'Cancelled');
$is_pending = !$is_completed && !$is_failed;
?>

<section class="py-5 bg-card">
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="kamadenu-card p-5 shadow-lg <?php echo $is_completed ? 'border-success' : ($is_pending ? 'border-warning' : 'border-danger'); ?>">
                    
                    <?php if ($is_completed): ?>
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success display-1"></i>
                        </div>
                        <div class="devotional-phrase fs-3 text-warning mb-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                        <h2 class="font-heading mb-3">Payment Complete &amp; Verified!</h2>
                        <p class="text-muted mb-4">Your payment has been successfully completed and verified in the database.</p>
                    <?php elseif ($is_pending): ?>
                        <div class="mb-4">
                            <i class="fas fa-clock text-warning display-1"></i>
                        </div>
                        <div class="devotional-phrase fs-3 text-warning mb-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                        <h2 class="font-heading mb-3">Contribution Submitted (Verification Pending)</h2>
                        <p class="text-muted mb-4">Your contribution request has been received. Status will update to <strong>Payment Complete</strong> once sanctuary admin verifies the transaction.</p>
                    <?php else: ?>
                        <div class="mb-4">
                            <i class="fas fa-times-circle text-danger display-1"></i>
                        </div>
                        <h2 class="font-heading mb-3 text-danger">Payment Unsuccessful or Cancelled</h2>
                        <p class="text-muted mb-4">The transaction could not be verified or was cancelled. Please try again or contact sanctuary support.</p>
                    <?php endif; ?>

                    <div class="bg-secondary-subtle p-3 rounded-4 mb-4 text-start font-ui small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Transaction Status:</span>
                            <?php if ($is_completed): ?>
                                <strong class="text-success"><i class="fas fa-check-circle me-1"></i> Payment Complete</strong>
                            <?php elseif ($is_pending): ?>
                                <strong class="text-warning-dark"><i class="fas fa-clock me-1"></i> Pending Verification</strong>
                            <?php else: ?>
                                <strong class="text-danger"><i class="fas fa-times-circle me-1"></i> Payment Failed</strong>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($payment_id)): ?>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Reference ID:</span>
                                <strong class="font-mono"><?php echo e($payment_id); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if ($amount > 0): ?>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Amount:</span>
                                <strong class="font-mono text-dark">₹<?php echo number_format($amount, 2); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($receipt_number)): ?>
                            <div class="d-flex justify-content-between py-1">
                                <span>Receipt Number:</span>
                                <strong class="font-mono text-warning-dark"><?php echo e($receipt_number); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/Kamadhenu-goushala/dashboard.php" class="btn btn-kamadenu-primary font-ui fw-bold py-3"><i class="fas fa-tachometer-alt me-2"></i> Go to My Dashboard</a>
                        <a href="/Kamadhenu-goushala/cows.php" class="btn btn-outline-warning font-ui fw-semibold py-2">Explore Resident Cows</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
