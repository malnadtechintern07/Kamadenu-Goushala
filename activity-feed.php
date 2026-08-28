<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Live Seva Logs
$seva_logs = $pdo->query("SELECT sl.*, s.title as seva_title, c.name as cow_name, c.cow_code FROM seva_logs sl JOIN seva s ON sl.seva_id = s.id LEFT JOIN cows c ON sl.cow_id = c.id ORDER BY sl.id DESC LIMIT 20")->fetchAll();

// Fetch Live Cow Sponsorships & Adoptions
$sponsorships = $pdo->query("SELECT s.*, sp.name as sponsor_name, c.name as cow_name, c.cow_code, c.photo FROM sponsorships s JOIN sponsors sp ON s.sponsor_id = sp.id JOIN cows c ON s.cow_id = c.id ORDER BY s.id DESC LIMIT 20")->fetchAll();

// Fetch Live Verified Donations
$donations = $pdo->query("SELECT * FROM donations WHERE status = 'Completed' ORDER BY id DESC LIMIT 20")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><i class="fas fa-stream me-2"></i> Live Gouseva Activity Feed</h1>
                <p class="text-white-50 mb-0">Real-time public wall showing recent cow adoptions, daily Sevas, and verified donations.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Navigation Tabs for Activity Categories -->
        <ul class="nav nav-pills justify-content-center mb-5 font-ui fw-bold" id="activityTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill px-4 me-2" id="all-tab" data-bs-toggle="tab" data-bs-target="#tab-all"><i class="fas fa-layer-group me-1"></i> All Activities</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 me-2" id="adoptions-tab" data-bs-toggle="tab" data-bs-target="#tab-adoptions"><i class="fas fa-cow me-1 text-warning"></i> Cow Adoptions</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 me-2" id="seva-tab" data-bs-toggle="tab" data-bs-target="#tab-seva"><i class="fas fa-praying-hands me-1 text-success"></i> Seva Sponsorships</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4" id="donations-tab" data-bs-toggle="tab" data-bs-target="#tab-donations"><i class="fas fa-heart me-1 text-danger"></i> Verified Donations</button>
            </li>
        </ul>

        <div class="tab-content" id="activityTabContent">
            <!-- All Activities Tab -->
            <div class="tab-pane fade show active" id="tab-all">
                <div class="row g-4">
                    <!-- Cow Adoptions -->
                    <?php foreach ($sponsorships as $sp): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-warning border-4">
                                <img src="/Kamadhenu-goushala/<?php echo e($sp['photo']); ?>" width="75" height="75" class="rounded-circle object-fit-cover shadow" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                                <div>
                                    <span class="badge bg-warning text-dark font-ui fw-bold mb-1"><i class="fas fa-cow me-1"></i> Cow Adoption</span>
                                    <h5 class="font-heading mb-1">
                                        <strong><?php echo e($sp['sponsor_name']); ?></strong> adopted <strong><?php echo e($sp['cow_name']); ?></strong> (<?php echo e($sp['cow_code']); ?>)
                                    </h5>
                                    <p class="small text-muted mb-0 font-ui">
                                        Duration: <?php echo $sp['duration_months']; ?> Month(s) &bull; 
                                        <span class="font-mono text-dark fw-bold">₹<?php echo number_format($sp['amount']); ?></span> &bull; 
                                        <span class="text-success fw-bold"><?php echo e($sp['status']); ?></span>
                                    </p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($sp['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Seva Sponsorships -->
                    <?php foreach ($seva_logs as $sl): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-success border-4">
                                <div class="rounded-circle bg-success-subtle text-success p-3 fs-3"><i class="fas fa-hands-praying"></i></div>
                                <div>
                                    <span class="badge bg-success font-ui fw-bold mb-1"><i class="fas fa-pray me-1"></i> Seva Sponsored</span>
                                    <h5 class="font-heading mb-1">
                                        <strong><?php echo e($sl['sponsor_name']); ?></strong> sponsored <strong><?php echo e($sl['seva_title']); ?></strong>
                                    </h5>
                                    <p class="small text-muted mb-0 font-ui">
                                        Beneficiary: <?php echo $sl['cow_name'] ? e($sl['cow_name']) : 'All Resident Cows'; ?> &bull; 
                                        <span class="font-mono text-dark fw-bold">₹<?php echo number_format($sl['amount_paid']); ?></span>
                                    </p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($sl['date_performed'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Verified Donations -->
                    <?php foreach ($donations as $d): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-danger border-4">
                                <div class="rounded-circle bg-danger-subtle text-danger p-3 fs-3"><i class="fas fa-heart"></i></div>
                                <div>
                                    <span class="badge bg-danger font-ui fw-bold mb-1"><i class="fas fa-donate me-1"></i> Donation</span>
                                    <h5 class="font-heading mb-1">
                                        <strong><?php echo e($d['is_anonymous'] ? 'Anonymous Devotee' : $d['donor_name']); ?></strong> donated for <strong><?php echo e($d['purpose']); ?></strong>
                                    </h5>
                                    <p class="small text-muted mb-0 font-ui">
                                        Amount: <span class="font-mono text-success fw-bold fs-6">₹<?php echo number_format($d['amount']); ?></span> &bull; 
                                        Receipt: <span class="badge bg-dark font-mono"><?php echo e($d['receipt_number']); ?></span>
                                    </p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($d['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Cow Adoptions Tab -->
            <div class="tab-pane fade" id="tab-adoptions">
                <div class="row g-4">
                    <?php foreach ($sponsorships as $sp): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-warning border-4">
                                <img src="/Kamadhenu-goushala/<?php echo e($sp['photo']); ?>" width="75" height="75" class="rounded-circle object-fit-cover shadow" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                                <div>
                                    <span class="badge bg-warning text-dark font-ui fw-bold mb-1">Cow Adoption</span>
                                    <h5 class="font-heading mb-1"><strong><?php echo e($sp['sponsor_name']); ?></strong> adopted <strong><?php echo e($sp['cow_name']); ?></strong></h5>
                                    <p class="small text-muted mb-0 font-ui">Duration: <?php echo $sp['duration_months']; ?> Month(s) &bull; Amount: ₹<?php echo number_format($sp['amount']); ?></p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($sp['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Seva Tab -->
            <div class="tab-pane fade" id="tab-seva">
                <div class="row g-4">
                    <?php foreach ($seva_logs as $sl): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-success border-4">
                                <div class="rounded-circle bg-success-subtle text-success p-3 fs-3"><i class="fas fa-hands-praying"></i></div>
                                <div>
                                    <span class="badge bg-success font-ui fw-bold mb-1">Seva Sponsored</span>
                                    <h5 class="font-heading mb-1"><strong><?php echo e($sl['sponsor_name']); ?></strong> &bull; <strong><?php echo e($sl['seva_title']); ?></strong></h5>
                                    <p class="small text-muted mb-0 font-ui">Beneficiary: <?php echo $sl['cow_name'] ? e($sl['cow_name']) : 'All Resident Cows'; ?> &bull; ₹<?php echo number_format($sl['amount_paid']); ?></p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($sl['date_performed'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Donations Tab -->
            <div class="tab-pane fade" id="tab-donations">
                <div class="row g-4">
                    <?php foreach ($donations as $d): ?>
                        <div class="col-md-6">
                            <div class="kamadenu-card p-4 d-flex align-items-center gap-3 border-start border-danger border-4">
                                <div class="rounded-circle bg-danger-subtle text-danger p-3 fs-3"><i class="fas fa-heart"></i></div>
                                <div>
                                    <span class="badge bg-danger font-ui fw-bold mb-1">Donation</span>
                                    <h5 class="font-heading mb-1"><strong><?php echo e($d['is_anonymous'] ? 'Anonymous Devotee' : $d['donor_name']); ?></strong> &bull; <?php echo e($d['purpose']); ?></h5>
                                    <p class="small text-muted mb-0 font-ui">Amount: ₹<?php echo number_format($d['amount']); ?> &bull; Receipt: <?php echo e($d['receipt_number']); ?></p>
                                    <small class="text-muted font-mono"><?php echo date('M d, Y', strtotime($d['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
