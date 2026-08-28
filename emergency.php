<?php
require_once __DIR__ . '/includes/header.php';

$campaigns = $pdo->query("SELECT ec.*, wn.phone_number as wa_phone_dir FROM emergency_campaigns ec LEFT JOIN whatsapp_numbers wn ON ec.whatsapp_number_id = wn.id ORDER BY ec.id DESC")->fetchAll();
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

                        <div class="d-flex flex-column gap-2 mt-4">
                            <?php if ($c['contact_method'] === 'both' || $c['contact_method'] === 'website' || empty($c['contact_method'])): ?>
                                <a href="/Kamadhenu-goushala/donate.php?campaign=<?php echo $c['id']; ?>" class="btn btn-danger w-100 py-3 font-ui fw-bold shadow"><i class="fas fa-hand-holding-heart me-2"></i> <?php echo __t('emergency_donate_now'); ?></a>
                            <?php endif; ?>
                            <?php if ($c['contact_method'] === 'whatsapp' || $c['contact_method'] === 'both'): ?>
                                <?php 
                                    $wa_phone = !empty($c['wa_phone_dir']) ? $c['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
                                    $wa_msg = !empty($c['whatsapp_message']) ? $c['whatsapp_message'] : "Hare Krishna! I would like to contribute to this emergency rescue campaign:\n- Campaign: " . $c['title'] . "\n- Urgency: " . $c['urgency_level'] . "\n\nPlease let me know how to proceed.";
                                    $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
                                ?>
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success w-100 py-3 font-ui fw-bold shadow text-center">
                                    <i class="fab fa-whatsapp me-2"></i> Donate via WhatsApp
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
