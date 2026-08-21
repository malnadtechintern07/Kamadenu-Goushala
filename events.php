<?php
require_once __DIR__ . '/includes/header.php';

$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><i class="fas fa-calendar-alt me-2"></i> Goushala Trust Events & Celebrations</h1>
                <p class="text-white-50 mb-0">Join us in sacred festivals, veterinary health camps, Gopashtami pujas, and bio-farming workshops.</p>
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
            <?php foreach ($events as $ev): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="kamadenu-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="position-relative">
                                <img src="<?php echo img_url($ev['photo']); ?>" class="cow-card-img" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'" alt="<?php echo e($ev['title']); ?>">
                                <span class="position-absolute top-0 end-0 m-3 badge <?php echo $ev['status'] === 'Upcoming' ? 'bg-warning text-dark' : 'bg-success'; ?> font-ui fw-bold shadow">
                                    <?php echo e($ev['status']); ?>

                                </span>
                            </div>
                            <div class="p-4">
                                <span class="badge bg-dark font-mono mb-2"><i class="fas fa-calendar-day me-1 text-warning"></i> <?php echo date('M d, Y', strtotime($ev['event_date'])); ?></span>
                                <h3 class="font-heading fs-4 mb-2"><?php echo e($ev['title']); ?></h3>
                                <?php if ($ev['title_kn']) echo "<p class='kn-text text-warning small fw-bold mb-2'>{$ev['title_kn']}</p>"; ?>
                                <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo e($ev['venue']); ?></p>
                                <p class="text-secondary small mb-0"><?php echo e(mb_strimwidth($ev['description'], 0, 110, '...')); ?></p>
                            </div>
                        </div>

                        <div class="p-4 pt-0 border-top mt-3">
                            <a href="/Kamadenu/event-detail.php?id=<?php echo $ev['id']; ?>" class="btn btn-kamadenu-primary w-100 font-ui fw-bold py-2 mt-3">
                                View Event Details & Register &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
