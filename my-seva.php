<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadhenu-goushala/login.php"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT sl.*, s.title as seva_title FROM seva_logs sl JOIN seva s ON sl.seva_id = s.id WHERE sl.user_id = ? ORDER BY sl.id DESC");
$stmt->execute([$user['id']]);
$sevas = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Sponsored Sevas</h1>
        <p class="text-white-50 mb-0">Record of your daily Gouseva sponsorships.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="kamadenu-card p-4">
            <?php if (empty($sevas)): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-3">No Seva sponsorships recorded.</p>
                    <a href="/Kamadhenu-goushala/seva.php" class="btn btn-warning font-ui fw-bold px-4">Sponsor Seva</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Seva Title</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sevas as $s): ?>
                                <tr>
                                    <td class="font-mono small"><?php echo e($s['date_performed']); ?></td>
                                    <td><strong><?php echo e($s['seva_title']); ?></strong></td>
                                    <td class="font-mono fw-bold text-success">₹<?php echo number_format($s['amount_paid'], 2); ?></td>
                                    <td><span class="badge bg-success"><?php echo e($s['status']); ?></span></td>
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
