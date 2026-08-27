<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT e.*, wn.phone_number as wa_phone_dir FROM events e LEFT JOIN whatsapp_numbers wn ON e.whatsapp_number_id = wn.id WHERE e.id = ? OR e.slug = ?");
$stmt->execute([$id, $id]);
$ev = $stmt->fetch();

if (!$ev) {
    header("Location: /Kamadenu/events.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <span class="badge bg-warning text-dark font-mono mb-2"><i class="fas fa-calendar-alt me-1"></i> <?php echo date('F d, Y', strtotime($ev['event_date'])); ?></span>
        <h1 class="font-heading text-warning mb-1"><?php echo e($ev['title']); ?></h1>
        <?php if ($ev['title_kn']) echo "<p class='kn-text text-warning fs-5 fw-bold mb-0'>{$ev['title_kn']}</p>"; ?>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="<?php echo img_url($ev['photo']); ?>" class="img-fluid rounded-4 shadow-lg border border-warning w-100" style="max-height: 400px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'">
            </div>
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5">
                    <?php
                    $st = $ev['status'] ?? 'Upcoming';
                    $b_cls = 'bg-warning text-dark';
                    $b_icn = 'fa-clock';
                    if ($st === 'Ongoing') {
                        $b_cls = 'bg-success text-white';
                        $b_icn = 'fa-bolt';
                    } elseif ($st === 'Completed') {
                        $b_cls = 'bg-secondary text-white';
                        $b_icn = 'fa-check-circle';
                    }
                    ?>
                    <span class="badge <?php echo $b_cls; ?> font-ui px-3 py-2 rounded-pill mb-3 fs-6">
                        <i class="fas <?php echo $b_icn; ?> me-1"></i> <?php echo e($st); ?> Event
                    </span>
                    <h2 class="font-heading mb-3"><?php echo e($ev['title']); ?></h2>
                    
                    <div class="bg-secondary-subtle p-3 rounded mb-4 font-ui">
                        <div class="mb-2"><i class="fas fa-calendar-day text-warning me-2"></i> <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($ev['event_date'])); ?></div>
                        <div><i class="fas fa-map-marker-alt text-danger me-2"></i> <strong>Venue:</strong> <?php echo e($ev['venue']); ?></div>
                    </div>

                    <p class="lead text-secondary mb-4"><?php echo e($ev['description']); ?></p>

                    <div class="d-flex flex-column gap-3 w-100">
                        <?php if ($st === 'Completed'): ?>
                            <div class="alert alert-secondary font-ui text-center mb-0 border-secondary border-opacity-25 rounded-3 py-3 shadow-sm">
                                <i class="fas fa-check-circle text-secondary me-2 fs-5 align-middle"></i>
                                <strong>Event Concluded:</strong> Registration is closed for this completed event.
                            </div>
                        <?php else: ?>
                            <?php if ($ev['contact_method'] === 'both' || $ev['contact_method'] === 'website' || empty($ev['contact_method'])): ?>
                                <a href="/Kamadenu/contact.php?event=<?php echo urlencode($ev['title']); ?>" class="btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow text-center">
                                    <i class="fas fa-hand-holding-heart me-2"></i> Register / Participate in Event
                                </a>
                            <?php endif; ?>
                            <?php if ($ev['contact_method'] === 'whatsapp' || $ev['contact_method'] === 'both'): ?>
                                <?php 
                                    $wa_phone = !empty($ev['wa_phone_dir']) ? $ev['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
                                    $wa_msg = !empty($ev['whatsapp_message']) ? $ev['whatsapp_message'] : "Hare Krishna! I would like to register/participate in this event:\n- Event: " . $ev['title'] . "\n- Date: " . date('F d, Y', strtotime($ev['event_date'])) . "\n\nPlease let me know how to proceed.";
                                    $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
                                ?>
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success btn-lg w-100 py-3 font-ui fw-bold shadow text-center">
                                    <i class="fab fa-whatsapp me-2"></i> Register via WhatsApp
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
