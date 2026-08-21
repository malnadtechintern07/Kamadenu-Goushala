<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$certificates = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Digital Certificates</h1>
        <p class="text-white-50 mb-0">Official appreciation and cow adoption certificates earned for your Gouseva.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (empty($certificates)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-certificate fs-1 text-muted mb-3 d-block"></i>
                    <h4>No certificates earned yet.</h4>
                    <p class="text-muted">Certificates are automatically generated when you sponsor cows or make contributions.</p>
                    <a href="/Kamadenu/donate.php" class="btn btn-warning font-ui fw-bold px-4 py-2 mt-2">Make Contribution</a>
                </div>
            <?php else: ?>
                <?php foreach ($certificates as $cert): ?>
                    <div class="col-md-6">
                        <div class="kamadenu-card p-4 text-center border-warning">
                            <div class="mb-3"><i class="fas fa-award text-warning display-4"></i></div>
                            <h4 class="font-heading mb-1"><?php echo e($cert['title']); ?></h4>
                            <p class="text-muted small mb-2">Awarded to <strong><?php echo e($cert['recipient_name']); ?></strong></p>
                            <span class="badge bg-dark font-mono mb-3"><?php echo e($cert['cert_code']); ?> &bull; Issued <?php echo date('M Y', strtotime($cert['issue_date'])); ?></span>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <a href="/Kamadenu/certificate-verify.php?code=<?php echo e($cert['cert_code']); ?>" target="_blank" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-qrcode me-1"></i> Verify Authenticity</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
