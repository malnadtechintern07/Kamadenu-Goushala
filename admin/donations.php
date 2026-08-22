<?php
require_once __DIR__ . '/header.php';

// Handle Donation Manual Verification Approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_donation_id'])) {
    $donation_id = intval($_POST['approve_donation_id']);
    
    // Fetch donation
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ? AND status = 'Pending Approval'");
    $stmt->execute([$donation_id]);
    $donation = $stmt->fetch();
    
    if ($donation) {
        $pdo->beginTransaction();
        try {
            // Update donation status
            $pdo->prepare("UPDATE donations SET status = 'Completed' WHERE id = ?")->execute([$donation_id]);
            
            // Set payment status to captured in payments table
            $pdo->prepare("UPDATE payments SET status = 'Captured' WHERE payment_id = ?")->execute([$donation['payment_id']]);
            
            // Generate Receipt
            $receipt_num = 'KGR-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
            $pdo->prepare("INSERT INTO receipts (donation_id, receipt_number, pdf_path) VALUES (?, ?, ?)")->execute([$donation_id, $receipt_num, 'uploads/receipts/' . $receipt_num . '.pdf']);
            
            // Check campaign associated from payments log
            $stmt_p = $pdo->prepare("SELECT raw_response FROM payments WHERE payment_id = ?");
            $stmt_p->execute([$donation['payment_id']]);
            $raw_resp_str = $stmt_p->fetchColumn();
            
            if ($raw_resp_str) {
                $raw_data = json_decode($raw_resp_str, true);
                if (!empty($raw_data['campaign_id'])) {
                    $campaign_id = intval($raw_data['campaign_id']);
                    $pdo->prepare("UPDATE emergency_campaigns SET raised_amount = raised_amount + ? WHERE id = ?")->execute([$donation['amount'], $campaign_id]);
                }
            }
            
            // Award Points & Notification if user exists
            if (!empty($donation['user_id'])) {
                $user_id = $donation['user_id'];
                $points = intval($donation['amount'] / 10);
                $pdo->prepare("UPDATE users SET gouseva_points = gouseva_points + ? WHERE id = ?")->execute([$points, $user_id]);
                $pdo->prepare("INSERT INTO gouseva_points (user_id, activity_type, points, description) VALUES (?, 'Donation', ?, ?)")->execute([$user_id, $points, "Donation of ₹{$donation['amount']} (Approved)"]);
                $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Donation Approved & Verified!', ?, 'success')")->execute([$user_id, "Your offline donation of ₹{$donation['amount']} has been verified and approved. Receipt #: {$receipt_num}"]);
            }
            
            log_audit($pdo, 'Approve Donation', 'donations', $donation_id);
            $pdo->commit();
            
            header("Location: /Kamadenu/admin/donations.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Approval failed: " . $e->getMessage();
        }
    }
}

$donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-donate text-warning me-2"></i> Verified Donations & Receipts</h3>
</div>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
<?php endif; ?>

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
                    <th>Action</th>
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
                        <td>
                            <?php if ($d['status'] === 'Pending Approval'): ?>
                                <span class="badge bg-warning text-dark mb-1 d-block"><?php echo e($d['status']); ?></span>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Confirm receipt of offline donation funds?');">
                                    <input type="hidden" name="approve_donation_id" value="<?php echo $d['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success py-0 px-2 font-ui" style="font-size: 0.75rem;"><i class="fas fa-check"></i> Approve</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success"><?php echo e($d['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button onclick="deleteAdminItem('donations', <?php echo $d['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
