<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ? OR cow_code = ?");
$stmt->execute([$id, $id]);
$cow = $stmt->fetch();

if (!$cow) {
    echo "<div class='container py-5 text-center'><h3>Cow record not found</h3><a href='/Kamadhenu-goushala/cows.php' class='btn btn-warning mt-3'>Back to Cows</a></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch Health Logs
$stmt = $pdo->prepare("SELECT * FROM cow_health WHERE cow_id = ? ORDER BY last_checkup_date DESC");
$stmt->execute([$cow['id']]);
$health_logs = $stmt->fetchAll();

// Fetch Journey Timeline
$stmt = $pdo->prepare("SELECT * FROM cow_journey WHERE cow_id = ? ORDER BY milestone_date ASC");
$stmt->execute([$cow['id']]);
$journey = $stmt->fetchAll();

// Fetch Monthly Updates
$stmt = $pdo->prepare("SELECT * FROM cow_updates WHERE cow_id = ? ORDER BY id DESC");
$stmt->execute([$cow['id']]);
$updates = $stmt->fetchAll();
?>

<?php 
    $cow_name = __td($cow, 'name');
    $cow_story = __td($cow, 'rescue_story');
?>
<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <span class="badge-cow-code me-2"><?php echo e($cow['cow_code']); ?></span>
                <span class="badge bg-warning text-dark font-ui font-semibold ms-1"><?php echo e($cow['breed']); ?> Breed</span>
                <h1 class="font-heading text-warning mt-2 mb-0"><?php echo e($cow_name); ?> Passport</h1>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2.5 flex-wrap">
                <a href="/Kamadhenu-goushala/adopt.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-kamadenu-primary btn-lg shadow d-flex align-items-center">
                    <i class="fas fa-heart me-2"></i> Sponsor <?php echo e($cow_name); ?> (₹<?php echo number_format($cow['monthly_sponsorship_amount']); ?>/mo)
                </a>
                <a href="/Kamadhenu-goushala/feed-cow.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-feed-cow btn-lg shadow d-flex align-items-center fw-bold">
                    <i class="fas fa-cookie-bite me-2"></i> Feed <?php echo e($cow_name); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Column: Passport Identity Card -->
            <div class="col-lg-5">
                <div class="kamadenu-card p-4 text-center sticky-top" style="top: 100px;">
                    <img src="<?php echo img_url($cow['photo']); ?>" alt="<?php echo e($cow_name); ?>" class="img-fluid rounded-4 shadow mb-4 hover-glow" style="max-height: 320px; object-fit: cover; width: 100%;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
                    
                    <h3 class="font-heading mb-1"><?php echo e($cow_name); ?></h3>
                    <p class="text-warning font-ui fw-bold mb-3">Official Passport ID: <?php echo e($cow['cow_code']); ?></p>


                    <div class="row g-2 text-start small border-top pt-3">
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Breed</span>
                            <strong class="font-ui fs-6"><?php echo e($cow['breed']); ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Gender</span>
                            <strong class="font-ui fs-6"><?php echo ucfirst($cow['gender']); ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Age</span>
                            <strong class="font-ui fs-6"><?php echo $cow['age_years']; ?> Yrs <?php echo $cow['age_months']; ?> Mos</strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Weight</span>
                            <strong class="font-ui fs-6"><?php echo $cow['weight_kg']; ?> kg</strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Rescue Date</span>
                            <strong class="font-ui fs-6"><?php echo date('M d, Y', strtotime($cow['rescue_date'])); ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted d-block">Health Condition</span>
                            <span class="badge bg-success px-2 py-1"><?php echo e($cow['health_status']); ?></span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="/Kamadhenu-goushala/adopt.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-kamadenu-primary flex-fill py-3 font-ui fw-bold fs-6">
                            <i class="fas fa-hand-holding-heart me-1.5"></i> Sponsor
                        </a>
                        <a href="/Kamadhenu-goushala/feed-cow.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-feed-cow flex-fill py-3 font-ui fw-bold fs-6">
                            <i class="fas fa-cookie-bite me-1.5"></i> Feed
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Rescue Story, Health Records & Journey Timeline -->
            <div class="col-lg-7">
                <!-- Rescue Story -->
                <div class="kamadenu-card p-4 mb-4">
                    <h4 class="font-heading text-warning mb-3"><i class="fas fa-book-open me-2"></i> Rescue & Sanctuary Chronicle</h4>
                    <p class="lead text-secondary"><?php echo e($cow_story); ?></p>
                </div>


                <!-- Cow Journey Visual Timeline -->
                <div class="kamadenu-card p-4 mb-4">
                    <h4 class="font-heading text-warning mb-3"><i class="fas fa-route me-2"></i> Sacred Journey Timeline</h4>
                    <div class="timeline-stepper">
                        <?php if (empty($journey)): ?>
                            <p class="text-muted">Journey details being documented by staff.</p>
                        <?php else: ?>
                            <?php foreach ($journey as $step): ?>
                                <div class="timeline-item">
                                    <span class="badge bg-warning-subtle text-dark font-mono mb-1"><?php echo e($step['stage']); ?> &bull; <?php echo date('M Y', strtotime($step['milestone_date'])); ?></span>
                                    <h5 class="font-heading mb-1"><?php echo e($step['title']); ?></h5>
                                    <p class="small text-muted mb-0"><?php echo e($step['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cow Health & Medical Logs -->
                <div class="kamadenu-card p-4 mb-4">
                    <h4 class="font-heading text-warning mb-3"><i class="fas fa-stethoscope me-2"></i> Veterinary Health & Diet Plan</h4>
                    <?php if (empty($health_logs)): ?>
                        <p class="text-muted">Health checks conducted regularly by Dr. Ramesh.</p>
                    <?php else: ?>
                        <?php $hl = $health_logs[0]; ?>
                        <div class="row g-3 bg-secondary-subtle p-3 rounded mb-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Dietary Plan</span>
                                <strong class="font-ui"><?php echo e($hl['dietary_plan']); ?></strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Medical Observations</span>
                                <strong class="font-ui text-success"><?php echo e($hl['medical_notes']); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Monthly Sponsor Updates -->
                <?php if (!empty($updates)): ?>
                <div class="kamadenu-card p-4">
                    <h4 class="font-heading text-warning mb-3"><i class="fas fa-newspaper me-2"></i> Sponsor Monthly Updates</h4>
                    <?php foreach ($updates as $up): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <span class="badge bg-dark font-mono"><?php echo e($up['update_month']); ?> <?php echo $up['update_year']; ?></span>
                            <h5 class="font-heading mt-2 mb-1"><?php echo e($up['title']); ?></h5>
                            <p class="small text-muted mb-0"><?php echo e($up['update_text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
