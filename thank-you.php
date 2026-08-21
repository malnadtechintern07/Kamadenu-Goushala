<?php
require_once __DIR__ . '/includes/header.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'donation';
$receipt_number = isset($_GET['receipt']) ? $_GET['receipt'] : 'KGR-2026-9901';
$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : 'pay_KGM_demo';
?>

<section class="py-5 bg-card">
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="kamadenu-card p-5 shadow-lg border-success">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success display-1"></i>
                    </div>

                    <div class="devotional-phrase fs-3 text-warning mb-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                    <h2 class="font-heading mb-3">Thank You for Your Sacred Contribution!</h2>
                    <p class="text-muted mb-4">Your payment has been verified and permanently saved in the Kamadenu Goushala database.</p>

                    <div class="bg-secondary-subtle p-3 rounded-4 mb-4 text-start font-ui small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Transaction Status:</span>
                            <strong class="text-success"><i class="fas fa-check me-1"></i> Verified & Completed</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Payment ID:</span>
                            <strong class="font-mono"><?php echo e($payment_id); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span>Receipt Number:</span>
                            <strong class="font-mono text-warning-dark"><?php echo e($receipt_number); ?></strong>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/Kamadenu/dashboard.php" class="btn btn-kamadenu-primary font-ui fw-bold py-3"><i class="fas fa-tachometer-alt me-2"></i> Go to My Dashboard</a>
                        <a href="/Kamadenu/cows.php" class="btn btn-outline-warning font-ui fw-semibold py-2">Explore Resident Cows</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
