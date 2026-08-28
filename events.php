<?php
require_once __DIR__ . '/includes/header.php';

$all_events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();

$ongoing_events = array_values(array_filter($all_events, function($ev) {
    return strtolower($ev['status'] ?? '') === 'ongoing';
}));

$upcoming_events = array_values(array_filter($all_events, function($ev) {
    $st = strtolower($ev['status'] ?? '');
    return $st === 'upcoming' || empty($st);
}));

$completed_events = array_values(array_filter($all_events, function($ev) {
    return strtolower($ev['status'] ?? '') === 'completed';
}));

function render_event_card_item($ev) {
    $etitle = __td($ev, 'title');
    $edesc = __td($ev, 'description');
    $status = $ev['status'] ?? 'Upcoming';
    
    $badge_class = 'bg-warning text-dark';
    $badge_icon = 'fa-clock';
    $btn_text = 'View Details &amp; Register &rarr;';
    
    if ($status === 'Ongoing') {
        $badge_class = 'bg-success text-white';
        $badge_icon = 'fa-bolt';
        $btn_text = 'Join Ongoing Event &rarr;';
    } elseif ($status === 'Completed') {
        $badge_class = 'bg-secondary text-white';
        $badge_icon = 'fa-check-circle';
        $btn_text = 'View Event Details &amp; Summary &rarr;';
    }
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="kamadenu-card h-100 d-flex flex-column justify-content-between shadow-sm">
            <div>
                <div class="position-relative">
                    <img src="<?php echo img_url($ev['photo']); ?>" class="cow-card-img" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'" alt="<?php echo e($etitle); ?>">
                    <span class="position-absolute top-0 end-0 m-3 badge <?php echo $badge_class; ?> font-ui fw-bold shadow-sm px-3 py-2">
                        <i class="fas <?php echo $badge_icon; ?> me-1"></i> <?php echo e($status); ?>
                    </span>
                </div>
                <div class="p-4">
                    <span class="badge bg-dark font-mono mb-2"><i class="fas fa-calendar-day me-1 text-warning"></i> <?php echo date('M d, Y', strtotime($ev['event_date'])); ?></span>
                    <h3 class="font-heading fs-4 mb-2"><?php echo e($etitle); ?></h3>
                    <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo e($ev['venue']); ?></p>
                    <p class="text-secondary small mb-0"><?php echo e(mb_strimwidth($edesc, 0, 110, '...')); ?></p>
                </div>
            </div>

            <div class="p-4 pt-0 border-top mt-3">
                <a href="/Kamadhenu-goushala/event-detail.php?id=<?php echo $ev['id']; ?>" class="btn <?php echo $status === 'Completed' ? 'btn-outline-secondary' : 'btn-kamadenu-primary'; ?> w-100 font-ui fw-bold py-2 mt-3">
                    <?php echo $btn_text; ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><i class="fas fa-calendar-alt me-2"></i> Goushala Trust Events &amp; Celebrations</h1>
                <p class="text-white-50 mb-0">Join us in sacred festivals, veterinary health camps, Gopashtami pujas, and bio-farming workshops.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="devotional-phrase fs-4 text-warning">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Navigation Pills -->
<section class="py-3 bg-body-tertiary border-bottom">
    <div class="container">
        <ul class="nav nav-pills justify-content-center gap-2 font-ui" id="eventStatusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 fw-bold" id="all-tab" data-bs-toggle="pill" data-bs-target="#all-events" type="button" role="tab" aria-controls="all-events" aria-selected="true">
                    <i class="fas fa-th-large me-1"></i> All Events <span class="badge bg-dark text-warning ms-1"><?php echo count($all_events); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-bold text-success" id="ongoing-tab" data-bs-toggle="pill" data-bs-target="#ongoing-events" type="button" role="tab" aria-controls="ongoing-events" aria-selected="false">
                    <i class="fas fa-bolt me-1 text-success"></i> Ongoing <span class="badge bg-success text-white ms-1"><?php echo count($ongoing_events); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-bold text-warning" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming-events" type="button" role="tab" aria-controls="upcoming-events" aria-selected="false">
                    <i class="fas fa-clock me-1 text-warning"></i> Upcoming <span class="badge bg-warning text-dark ms-1"><?php echo count($upcoming_events); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-bold text-secondary" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed-events" type="button" role="tab" aria-controls="completed-events" aria-selected="false">
                    <i class="fas fa-check-circle me-1 text-secondary"></i> Completed <span class="badge bg-secondary text-white ms-1"><?php echo count($completed_events); ?></span>
                </button>
            </li>
        </ul>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="tab-content" id="eventStatusTabsContent">
            
            <!-- ALL EVENTS TAB -->
            <div class="tab-pane fade show active" id="all-events" role="tabpanel" aria-labelledby="all-tab">
                
                <!-- 1. ONGOING EVENTS SECTION -->
                <?php if (!empty($ongoing_events)): ?>
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between border-bottom border-success border-2 pb-2 mb-4">
                            <h2 class="font-heading fs-3 text-success mb-0">
                                <i class="fas fa-bolt me-2"></i> Ongoing Events &amp; Active Programs
                            </h2>
                            <span class="badge bg-success font-ui px-3 py-2 rounded-pill"><i class="fas fa-signal me-1"></i> Live Now</span>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($ongoing_events as $ev) { render_event_card_item($ev); } ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 2. UPCOMING EVENTS SECTION -->
                <?php if (!empty($upcoming_events)): ?>
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between border-bottom border-warning border-2 pb-2 mb-4">
                            <h2 class="font-heading fs-3 text-warning mb-0">
                                <i class="fas fa-calendar-alt me-2"></i> Upcoming Events &amp; Festivals
                            </h2>
                            <span class="badge bg-warning text-dark font-ui px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i> Scheduled</span>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($upcoming_events as $ev) { render_event_card_item($ev); } ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. COMPLETED EVENTS SECTION -->
                <?php if (!empty($completed_events)): ?>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between border-bottom border-secondary border-2 pb-2 mb-4">
                            <h2 class="font-heading fs-3 text-secondary mb-0">
                                <i class="fas fa-check-circle me-2"></i> Completed Events &amp; Past Celebrations
                            </h2>
                            <span class="badge bg-secondary font-ui px-3 py-2 rounded-pill"><i class="fas fa-archive me-1"></i> Archive</span>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($completed_events as $ev) { render_event_card_item($ev); } ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($all_events)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="font-heading text-muted">No trust events found</h4>
                        <p class="text-secondary">Please check back soon for upcoming Goushala celebrations.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- ONGOING EVENTS TAB ONLY -->
            <div class="tab-pane fade" id="ongoing-events" role="tabpanel" aria-labelledby="ongoing-tab">
                <div class="d-flex align-items-center justify-content-between border-bottom border-success border-2 pb-2 mb-4">
                    <h2 class="font-heading fs-3 text-success mb-0"><i class="fas fa-bolt me-2"></i> Ongoing Events</h2>
                    <span class="badge bg-success font-ui px-3 py-2 rounded-pill"><?php echo count($ongoing_events); ?> Active</span>
                </div>
                <?php if (!empty($ongoing_events)): ?>
                    <div class="row g-4">
                        <?php foreach ($ongoing_events as $ev) { render_event_card_item($ev); } ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 kamadenu-card">
                        <i class="fas fa-info-circle fs-1 text-success mb-3 d-block"></i>
                        <h4 class="font-heading text-secondary">No events currently ongoing</h4>
                        <p class="text-muted">Check out our upcoming events tab to participate in future programs!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- UPCOMING EVENTS TAB ONLY -->
            <div class="tab-pane fade" id="upcoming-events" role="tabpanel" aria-labelledby="upcoming-tab">
                <div class="d-flex align-items-center justify-content-between border-bottom border-warning border-2 pb-2 mb-4">
                    <h2 class="font-heading fs-3 text-warning mb-0"><i class="fas fa-clock me-2"></i> Upcoming Events</h2>
                    <span class="badge bg-warning text-dark font-ui px-3 py-2 rounded-pill"><?php echo count($upcoming_events); ?> Scheduled</span>
                </div>
                <?php if (!empty($upcoming_events)): ?>
                    <div class="row g-4">
                        <?php foreach ($upcoming_events as $ev) { render_event_card_item($ev); } ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 kamadenu-card">
                        <i class="fas fa-calendar-day fs-1 text-warning mb-3 d-block"></i>
                        <h4 class="font-heading text-secondary">No upcoming events scheduled right now</h4>
                        <p class="text-muted">Stay tuned as new events will be announced soon.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- COMPLETED EVENTS TAB ONLY -->
            <div class="tab-pane fade" id="completed-events" role="tabpanel" aria-labelledby="completed-tab">
                <div class="d-flex align-items-center justify-content-between border-bottom border-secondary border-2 pb-2 mb-4">
                    <h2 class="font-heading fs-3 text-secondary mb-0"><i class="fas fa-check-circle me-2"></i> Completed Events</h2>
                    <span class="badge bg-secondary font-ui px-3 py-2 rounded-pill"><?php echo count($completed_events); ?> Archived</span>
                </div>
                <?php if (!empty($completed_events)): ?>
                    <div class="row g-4">
                        <?php foreach ($completed_events as $ev) { render_event_card_item($ev); } ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 kamadenu-card">
                        <i class="fas fa-archive fs-1 text-secondary mb-3 d-block"></i>
                        <h4 class="font-heading text-secondary">No completed events in archive yet</h4>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
