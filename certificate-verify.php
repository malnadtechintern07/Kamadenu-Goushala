<?php
require_once __DIR__ . '/includes/header.php';

$code = isset($_GET['code']) ? trim($_GET['code']) : '';
$cert = null;

if (!empty($code)) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE cert_code = ?");
    $stmt->execute([$code]);
    $cert = $stmt->fetch();
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">Digital Certificate Verification</h1>
        <p class="text-white-50 mb-0">Verify the authenticity of Kamadenu Goushala Trust appreciation & adoption certificates.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5 text-center mb-4">
                    <h3 class="font-heading mb-3">Enter Certificate Code</h3>
                    <form method="GET" action="/Kamadhenu-goushala/certificate-verify.php" class="d-flex gap-2 mb-4">
                        <input type="text" name="code" class="form-control form-control-lg font-mono text-center" placeholder="e.g. KGC-2026-9812" value="<?php echo e($code); ?>" required>
                        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4">Verify</button>
                    </form>

                    <?php if (!empty($code)): ?>
                        <?php if ($cert): ?>
                            <div class="alert alert-success border-success text-start p-4 rounded-4">
                                <h4 class="font-heading text-success mb-2"><i class="fas fa-check-circle me-2"></i> Authentic Certificate Verified</h4>
                                <div class="font-ui small">
                                    <div><strong>Recipient Name:</strong> <?php echo e($cert['recipient_name']); ?></div>
                                    <div><strong>Certificate Title:</strong> <?php echo e($cert['title']); ?></div>
                                    <div><strong>Certificate Code:</strong> <span class="font-mono"><?php echo e($cert['cert_code']); ?></span></div>
                                    <div><strong>Issue Date:</strong> <?php echo date('F d, Y', strtotime($cert['issue_date'])); ?></div>
                                    <div><strong>Issuer:</strong> Kamadenu Goushala Charitable Trust</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger text-start p-4 rounded-4">
                                <h4 class="font-heading text-danger mb-2"><i class="fas fa-times-circle me-2"></i> Invalid Certificate Code</h4>
                                <p class="small mb-0">No matching certificate record found in our database.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
