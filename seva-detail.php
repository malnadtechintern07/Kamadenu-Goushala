<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
$stmt->execute([$id]);
$seva = $stmt->fetch();

if (!$seva) {
    header("Location: /Kamadenu/seva.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo e($seva['title']); ?></h1>
        <p class="text-white-50 mb-0"><?php echo e($seva['category']); ?> Sacred Seva</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="kamadenu-card p-4 p-md-5 text-center">
                    <div class="fs-1 text-warning mb-3"><i class="fas <?php echo e($seva['icon']); ?>"></i></div>
                    <h2 class="font-heading mb-2"><?php echo e($seva['title']); ?></h2>
                    <?php if ($seva['title_kn']) echo "<p class='kn-text text-warning fs-5 fw-bold mb-3'>{$seva['title_kn']}</p>"; ?>
                    <p class="lead text-secondary mb-4"><?php echo e($seva['description']); ?></p>

                    <div class="bg-secondary-subtle p-3 rounded-4 mb-4">
                        <span class="text-muted d-block small font-ui">Suggested Contribution Amount</span>
                        <div class="fs-1 fw-bold text-dark font-mono">₹<?php echo number_format($seva['suggested_amount']); ?></div>
                    </div>

                    <a href="/Kamadenu/checkout.php?type=seva&seva_id=<?php echo $seva['id']; ?>&amount=<?php echo $seva['suggested_amount']; ?>" class="btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow">
                        <i class="fas fa-hand-holding-heart me-2"></i> Sponsor This Seva Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
