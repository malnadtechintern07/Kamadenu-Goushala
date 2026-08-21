<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM emergency_campaigns WHERE id = ?");
$stmt->execute([$id]);
$campaign = $stmt->fetch();

if (!$campaign) {
    header("Location: /Kamadenu/emergency.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <span class="badge bg-danger font-ui text-uppercase mb-2"><i class="fas fa-exclamation-circle me-1"></i> <?php echo e($campaign['urgency_level']); ?> Urgency Campaign</span>
        <h1 class="font-heading text-warning mb-1"><?php echo e($campaign['title']); ?></h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="<?php echo e($campaign['photo']); ?>" class="img-fluid rounded-4 shadow-lg border border-warning w-100" onerror="this.src='https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=600&q=80'">
            </div>
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading text-danger mb-3"><?php echo e($campaign['title']); ?></h3>
                    <p class="lead text-secondary mb-4"><?php echo e($campaign['story']); ?></p>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between font-ui fw-bold mb-1">
                            <span>Goal: ₹<?php echo number_format($campaign['target_amount']); ?></span>
                            <span class="text-success">Raised: ₹<?php echo number_format($campaign['raised_amount']); ?></span>
                        </div>
                        <?php $pct = min(100, round(($campaign['raised_amount'] / $campaign['target_amount']) * 100)); ?>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated font-mono fw-bold" style="width: <?php echo $pct; ?>%;"><?php echo $pct; ?>%</div>
                        </div>
                    </div>

                    <a href="/Kamadenu/donate.php?campaign=<?php echo $campaign['id']; ?>" class="btn btn-danger btn-lg w-100 py-3 font-ui fw-bold shadow"><i class="fas fa-hand-holding-heart me-2"></i> Donate to this Rescue Relief</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
