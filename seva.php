<?php
require_once __DIR__ . '/includes/header.php';

$seva_items = $pdo->query("SELECT s.*, wn.phone_number as wa_phone_dir FROM seva s LEFT JOIN whatsapp_numbers wn ON s.whatsapp_number_id = wn.id WHERE s.is_active = 1 ORDER BY s.id ASC")->fetchAll();
$recent_logs = $pdo->query("SELECT sl.*, s.title as seva_title, c.name as cow_name FROM seva_logs sl JOIN seva s ON sl.seva_id = s.id LEFT JOIN cows c ON sl.cow_id = c.id ORDER BY sl.id DESC LIMIT 8")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_seva'); ?></h1>
                <p class="text-white-50 mb-0">Participate in daily sacred Gouseva activities, feeding, and Vedic worship.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($seva_items as $s): ?>
                <?php 
                    $stitle = __td($s, 'title');
                    $sdesc = __td($s, 'description');
                ?>
                <div class="col-md-6 col-lg-3">
                    <div class="kamadenu-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-1 text-warning mb-3"><i class="fas <?php echo e($s['icon']); ?>"></i></div>
                            <h3 class="font-heading fs-5 mb-2"><?php echo e($stitle); ?></h3>
                            <p class="small text-muted mb-3"><?php echo e($sdesc); ?></p>
                        </div>

                        <div>
                            <div class="fs-3 fw-bold text-dark font-mono mb-3">₹<?php echo number_format($s['suggested_amount']); ?></div>
                            <div class="d-flex flex-column gap-2 w-100">
                                <?php if ($s['contact_method'] === 'both' || $s['contact_method'] === 'website' || empty($s['contact_method'])): ?>
                                    <a href="/Kamadhenu-goushala/checkout.php?type=seva&seva_id=<?php echo $s['id']; ?>&amount=<?php echo $s['suggested_amount']; ?>" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">
                                        <i class="fas fa-hand-holding-heart me-1"></i> Sponsor Seva
                                    </a>
                                <?php endif; ?>
                                <?php if ($s['contact_method'] === 'whatsapp' || $s['contact_method'] === 'both'): ?>
                                    <?php 
                                        $wa_phone = !empty($s['wa_phone_dir']) ? $s['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
                                        $wa_msg = !empty($s['whatsapp_message']) ? $s['whatsapp_message'] : "Hare Krishna! I would like to sponsor this seva:\n- Seva: " . $s['title'] . "\n- Amount: ₹" . number_format($s['suggested_amount']) . "\n\nPlease let me know how to proceed.";
                                        $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
                                    ?>
                                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success w-100 font-ui fw-bold">
                                        <i class="fab fa-whatsapp me-1"></i> Sponsor via WhatsApp
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Live Seva Logs Timeline -->
        <div class="mt-5 pt-4">
            <h3 class="font-heading mb-4 text-warning"><i class="fas fa-history me-2"></i> Live Gouseva Logs & Updates</h3>
            <div class="row g-3">
                <?php foreach ($recent_logs as $log): ?>
                    <div class="col-md-6">
                        <div class="kamadenu-card p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning text-dark p-3 fs-4"><i class="fas fa-praying-hands"></i></div>
                            <div>
                                <h5 class="font-heading mb-1"><?php echo e($log['seva_title']); ?></h5>
                                <p class="small text-muted mb-0">
                                    Sponsored by <strong><?php echo e($log['sponsor_name']); ?></strong> 
                                    <?php if ($log['cow_name']) echo "for <strong>{$log['cow_name']}</strong>"; ?>
                                    &bull; <span class="badge bg-success"><?php echo e($log['status']); ?></span>
                                </p>
                                <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($log['date_performed'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
