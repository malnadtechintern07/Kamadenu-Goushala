<?php
require_once __DIR__ . '/includes/header.php';

$campaigns = $pdo->query("SELECT * FROM emergency_campaigns ORDER BY id DESC")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_emergency'); ?></h1>
        <p class="text-white-50 mb-0">Critical rescue missions requiring immediate medical care, fodder relief, and surgical attention.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($campaigns as $c): ?>
                <?php 
                    $ctitle = __td($c, 'title');
                    $cstory = __td($c, 'story');
                ?>
                <div class="col-lg-6">
                    <div class="kamadenu-card h-100 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-danger font-ui px-3 py-2 text-uppercase"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo e($c['urgency_level']); ?> Urgency</span>
                            <span class="badge bg-secondary font-mono"><?php echo e($c['status']); ?></span>
                        </div>

                        <h3 class="font-heading text-danger mb-2"><?php echo e($ctitle); ?></h3>
                        <p class="text-muted mb-4"><?php echo e($cstory); ?></p>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between font-ui fw-bold mb-1">
                                <span><?php echo __t('funding_target'); ?>: ₹<?php echo number_format($c['target_amount']); ?></span>
                                <span class="text-success"><?php echo __t('amount_raised'); ?>: ₹<?php echo number_format($c['raised_amount']); ?></span>
                            </div>
                            <?php $pct = min(100, round(($c['raised_amount'] / $c['target_amount']) * 100)); ?>
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated font-mono fw-bold" style="width: <?php echo $pct; ?>%;"><?php echo $pct; ?>%</div>
                            </div>
                        </div>

                        <a href="/Kamadenu/donate.php?campaign=<?php echo $c['id']; ?>" class="btn btn-danger w-100 py-3 font-ui fw-bold shadow"><i class="fas fa-hand-holding-heart me-2"></i> <?php echo __t('emergency_donate_now'); ?></a>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
