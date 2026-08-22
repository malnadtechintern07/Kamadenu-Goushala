<?php
require_once __DIR__ . '/includes/header.php';

$stories = $pdo->query("SELECT * FROM stories WHERE status = 'Published' ORDER BY id DESC")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_stories'); ?></h1>
        <p class="text-white-50 mb-0">Inspiring rescue chronicles, cow transformations, and Vedic wisdom from Kamadenu Goushala.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($stories as $st): ?>
                <?php 
                    $sttitle = __td($st, 'title');
                    $stsummary = __td($st, 'summary');
                    $stcontent = __td($st, 'content');
                ?>
                <div class="col-md-6">
                    <div class="kamadenu-card h-100 p-4">
                        <span class="badge bg-warning text-dark font-mono mb-2"><?php echo date('M d, Y', strtotime($st['published_at'])); ?></span>
                        <h3 class="font-heading fs-4 mb-2"><?php echo e($sttitle); ?></h3>
                        <p class="text-muted small mb-3"><?php echo e($stsummary); ?></p>
                        <div class="lh-relaxed text-secondary mb-4"><?php echo $stcontent; ?></div>
                        <span class="small text-warning font-ui"><i class="fas fa-pen-nib me-1"></i> By <?php echo e($st['author']); ?></span>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
