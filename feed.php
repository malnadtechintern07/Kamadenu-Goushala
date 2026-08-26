<?php
require_once __DIR__ . '/includes/header.php';

$feed_items = $pdo->query("SELECT f.*, wn.phone_number as wa_phone_dir FROM feed_items f LEFT JOIN whatsapp_numbers wn ON f.whatsapp_number_id = wn.id ORDER BY f.id ASC")->fetchAll();
$recent_logs = $pdo->query("SELECT fl.*, fi.title as item_title FROM feed_logs fl JOIN feed_items fi ON fl.feed_item_id = fi.id ORDER BY fl.id DESC LIMIT 8")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_feed'); ?></h1>
                <p class="text-white-50 mb-0">Sponsor nutritious green fodder, wheat bran, and health feasts for our rescued cattle.</p>
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
            <?php foreach ($feed_items as $f): ?>
                <?php 
                    $ftitle = __td($f, 'title');
                    $fdesc = __td($f, 'description');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="kamadenu-card h-100 d-flex flex-column justify-content-between overflow-hidden">
                        <div class="position-relative">
                            <img src="<?php echo img_url($f['image']); ?>" class="product-card-img" alt="<?php echo e($ftitle); ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h3 class="font-heading fs-5 mb-2"><?php echo e($ftitle); ?></h3>
                                <p class="small text-muted mb-4"><?php echo e($fdesc); ?></p>
                            </div>

                            <div>
                                <div class="fs-3 fw-bold text-dark font-mono mb-3">₹<?php echo number_format($f['cost']); ?></div>
                                <div class="d-flex flex-column gap-2 w-100">
                                    <?php if ($f['contact_method'] === 'both' || $f['contact_method'] === 'website' || empty($f['contact_method'])): ?>
                                        <a href="/Kamadenu/checkout.php?type=feed&feed_id=<?php echo $f['id']; ?>&amount=<?php echo $f['cost']; ?>" class="btn btn-kamadenu-primary w-100 font-ui fw-bold d-flex align-items-center justify-content-center">
                                            <i class="fas fa-hand-holding-heart me-2"></i> Sponsor Feed
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($f['contact_method'] === 'whatsapp' || $f['contact_method'] === 'both'): ?>
                                        <?php 
                                            $wa_phone = !empty($f['wa_phone_dir']) ? $f['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
                                            $wa_msg = !empty($f['whatsapp_message']) ? $f['whatsapp_message'] : "Hare Krishna! I would like to sponsor this feed:\n- Item: " . $f['title'] . "\n- Cost: ₹" . number_format($f['cost']) . "\n\nPlease let me know how to proceed.";
                                            $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
                                        ?>
                                        <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success w-100 font-ui fw-bold d-flex align-items-center justify-content-center">
                                            <i class="fab fa-whatsapp me-2"></i> Sponsor via WhatsApp
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Recent Feed Logs -->
        <?php if (!empty($recent_logs)): ?>
            <div class="mt-5 pt-4">
                <h3 class="font-heading mb-4 text-warning"><i class="fas fa-history me-2"></i> Recent Feeding Contributions</h3>
                <div class="row g-3">
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="kamadenu-card p-3 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning text-dark p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fas fa-cow"></i></div>
                                <div>
                                    <h5 class="font-heading mb-1 fs-6"><?php echo e($log['item_title']); ?></h5>
                                    <p class="small text-muted mb-0">
                                        Sponsored by <strong><?php echo e($log['sponsor_name']); ?></strong> 
                                        &bull; <span class="badge bg-success"><?php echo e($log['status']); ?></span>
                                    </p>
                                    <small class="text-muted font-mono fs-7"><?php echo date('M d, Y', strtotime($log['date_sponsored'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
