<?php
require_once __DIR__ . '/header.php';

// Handle Sponsorship Manual Verification Approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_sponsorship_id'])) {
    $sponsorship_id = intval($_POST['approve_sponsorship_id']);
    
    // Fetch sponsorship
    $stmt = $pdo->prepare("SELECT s.*, sp.user_id, sp.name as sponsor_name FROM sponsorships s JOIN sponsors sp ON s.sponsor_id = sp.id WHERE s.id = ? AND s.status = 'Pending Approval'");
    $stmt->execute([$sponsorship_id]);
    $sponsorship = $stmt->fetch();
    
    if ($sponsorship) {
        $pdo->beginTransaction();
        try {
            // Update status to 'Active'
            $pdo->prepare("UPDATE sponsorships SET status = 'Active' WHERE id = ?")->execute([$sponsorship_id]);
            
            // Set payment status to captured in payments table
            $pdo->prepare("UPDATE payments SET status = 'Captured' WHERE payment_id = ?")->execute([$sponsorship['payment_id']]);
            
            // Update Cow status
            $pdo->prepare("UPDATE cows SET adoption_status = 'Sponsored' WHERE id = ?")->execute([$sponsorship['cow_id']]);
            
            // Issue Certificate
            $cert_code = 'KGC-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
            $start_date = date('Y-m-d');
            $pdo->prepare("INSERT INTO certificates (user_id, cert_code, cert_type, title, recipient_name, issue_date) VALUES (?, ?, 'Sponsorship', 'Cow Adoption & Sponsorship Certificate', ?, ?)")->execute([$sponsorship['user_id'], $cert_code, $sponsorship['sponsor_name'], $start_date]);
            
            // Create user notification
            if (!empty($sponsorship['user_id'])) {
                $user_id = $sponsorship['user_id'];
                $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Sponsorship Approved!', 'Your cow sponsorship request has been verified and is now Active. Your Cow Adoption Certificate has been issued successfully!', 'success')")->execute([$user_id]);
            }
            
            log_audit($pdo, 'Approve Sponsorship', 'sponsorships', $sponsorship_id);
            $pdo->commit();
            
            header("Location: /Kamadhenu-goushala/admin/sponsors.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Approval failed: " . $e->getMessage();
        }
    }
}

$sponsorships = $pdo->query("SELECT s.*, sp.name as sponsor_name, sp.email, sp.phone, c.name as cow_name, c.cow_code FROM sponsorships s JOIN sponsors sp ON s.sponsor_id = sp.id JOIN cows c ON s.cow_id = c.id ORDER BY s.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-hand-holding-heart text-warning me-2"></i> Active Sponsors & Cow Adoptions</h3>
</div>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Sponsor Name</th>
                    <th>Contact</th>
                    <th>Sponsored Cow</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sponsorships as $sp): ?>
                    <tr>
                        <td><strong><?php echo e($sp['sponsor_name']); ?></strong></td>
                        <td class="small"><?php echo e($sp['email']); ?><br><?php echo e($sp['phone']); ?></td>
                        <td><strong><?php echo e($sp['cow_name']); ?></strong> (<?php echo e($sp['cow_code']); ?>)</td>
                        <td><?php echo $sp['duration_months']; ?> Mos</td>
                        <td class="font-mono small"><?php echo e($sp['start_date']); ?></td>
                        <td class="font-mono small"><?php echo e($sp['end_date']); ?></td>
                        <td class="font-mono fw-bold text-dark">₹<?php echo number_format($sp['amount']); ?></td>
                        <td>
                            <?php if ($sp['status'] === 'Pending Approval'): ?>
                                <span class="badge bg-warning text-dark mb-1 d-block"><?php echo e($sp['status']); ?></span>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Approve this cow sponsorship?');">
                                    <input type="hidden" name="approve_sponsorship_id" value="<?php echo $sp['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success py-0 px-2 font-ui" style="font-size: 0.75rem;"><i class="fas fa-check"></i> Approve</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success"><?php echo e($sp['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button onclick="deleteAdminItem('sponsorships', <?php echo $sp['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
