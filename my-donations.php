<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadhenu-goushala/login.php?redirect=" . urlencode('/Kamadhenu-goushala/my-donations.php') . "&msg=login_required"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$donations = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT fl.*, fc.name as cow_name, fc.cow_code FROM feeding_cow_logs fl JOIN feeding_cows fc ON fl.feeding_cow_id = fc.id WHERE fl.user_id = ? ORDER BY fl.id DESC");
$stmt->execute([$user['id']]);
$feeding_contributions = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Donation History</h1>
        <p class="text-white-50 mb-0">Your past devotional contributions and 80G tax exemption receipts.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: General Donations -->
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 h-100">
                    <h3 class="font-heading mb-3 text-warning"><i class="fas fa-hand-holding-usd me-2"></i> General Donations</h3>
                    <?php if (empty($donations)): ?>
                        <p class="text-muted py-4 text-center">No general donations recorded yet.</p>
                    <?php else: ?> 
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Purpose</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donations as $d): ?>
                                        <tr>
                                            <td class="font-mono small text-nowrap"><?php echo date('M d, Y', strtotime($d['created_at'])); ?></td>
                                            <td><small class="fw-bold d-block text-truncate" style="max-width: 150px;"><?php echo e($d['purpose']); ?></small></td>
                                            <td class="font-mono fw-bold text-success">₹<?php echo number_format($d['amount'], 2); ?></td>
                                            <td>
                                                <?php if ($d['status'] === 'Completed' || $d['status'] === 'Paid'): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Payment Complete</span>
                                                <?php elseif ($d['status'] === 'Pending Approval' || $d['status'] === 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> <?php echo e($d['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side: Cattle Feeding Contributions -->
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 h-100">
                    <h3 class="font-heading mb-3 text-warning"><i class="fas fa-cookie-bite me-2"></i> Cattle Feeding Contributions</h3>
                    <?php if (empty($feeding_contributions)): ?>
                        <p class="text-muted py-4 text-center">No feeding contributions recorded yet.</p>
                    <?php else: ?> 
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Cattle</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feeding_contributions as $fc_log): ?>
                                        <tr>
                                            <td class="font-mono small text-nowrap"><?php echo date('M d, Y', strtotime($fc_log['date_sponsored'])); ?></td>
                                            <td>
                                                <small class="d-block">
                                                    <strong><?php echo e($fc_log['cow_name']); ?></strong> 
                                                    <span class="badge-cow-code text-nowrap"><?php echo e($fc_log['cow_code']); ?></span>
                                                </small>
                                            </td>
                                            <td class="font-mono fw-bold text-success">₹<?php echo number_format($fc_log['amount_paid'], 2); ?></td>
                                            <td>
                                                <?php if ($fc_log['status'] === 'Completed' || $fc_log['status'] === 'Paid'): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Payment Complete</span>
                                                <?php elseif ($fc_log['status'] === 'Pending Approval' || $fc_log['status'] === 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> <?php echo e($fc_log['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
