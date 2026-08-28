<?php
require_once __DIR__ . '/includes/header.php';

if (!is_user_logged_in()) {
    header("Location: /Kamadhenu-goushala/login.php?redirect=" . urlencode('/Kamadhenu-goushala/dashboard.php') . "&msg=login_required");
    exit;
}

$user = current_user($pdo);

// Fetch User Sponsored Cows
$stmt = $pdo->prepare("SELECT s.*, c.name as cow_name, c.cow_code, c.breed, c.photo FROM sponsorships s JOIN cows c ON s.cow_id = c.id JOIN sponsors sp ON s.sponsor_id = sp.id WHERE sp.user_id = ?");
$stmt->execute([$user['id']]);
$sponsored_cows = $stmt->fetchAll();

// Fetch User Donations
$stmt = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$stmt->execute([$user['id']]);
$user_donations = $stmt->fetchAll();

// Fetch User Certificates
$stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$certificates = $stmt->fetchAll();

// Fetch User Badges
$stmt = $pdo->prepare("SELECT b.* FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ?");
$stmt->execute([$user['id']]);
$badges = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <span class="badge bg-warning text-dark font-ui fw-bold mb-1"><i class="fas fa-star me-1"></i> <?php echo $user['gouseva_points']; ?> Gouseva Points</span>
                <h1 class="font-heading text-warning mb-0">Welcome, <?php echo e($user['name']); ?>!</h1>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="/Kamadhenu-goushala/donate.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-heart me-1"></i> Make Donation</a>
                <a href="/Kamadhenu-goushala/adopt.php" class="btn btn-outline-warning font-ui ms-2"><i class="fas fa-cow me-1"></i> Sponsor Cow</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- KPI Metrics Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="kamadenu-card p-4 text-center">
                    <i class="fas fa-cow fs-1 text-warning mb-2"></i>
                    <div class="fs-2 fw-bold font-mono"><?php echo count($sponsored_cows); ?></div>
                    <div class="text-muted small font-ui fw-bold">Sponsored Cows</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kamadenu-card p-4 text-center">
                    <i class="fas fa-hand-holding-heart fs-1 text-danger mb-2"></i>
                    <div class="fs-2 fw-bold font-mono"><?php echo count($user_donations); ?></div>
                    <div class="text-muted small font-ui fw-bold">Total Donations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kamadenu-card p-4 text-center">
                    <i class="fas fa-certificate fs-1 text-success mb-2"></i>
                    <div class="fs-2 fw-bold font-mono"><?php echo count($certificates); ?></div>
                    <div class="text-muted small font-ui fw-bold">Certificates Earned</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kamadenu-card p-4 text-center">
                    <i class="fas fa-award fs-1 text-warning mb-2"></i>
                    <div class="fs-2 fw-bold font-mono"><?php echo count($badges); ?></div>
                    <div class="text-muted small font-ui fw-bold">Gouseva Badges</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Sponsored Cows -->
            <div class="col-lg-7">
                <div class="kamadenu-card p-4 mb-4">
                    <h4 class="font-heading mb-3"><i class="fas fa-cow text-warning me-2"></i> My Adopted & Sponsored Cows</h4>
                    <?php if (empty($sponsored_cows)): ?>
                        <div class="text-center py-4 text-muted">
                            <p>You have not sponsored any cows yet.</p>
                            <a href="/Kamadhenu-goushala/adopt.php" class="btn btn-outline-warning rounded-pill btn-sm">Sponsor a Cow Now</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($sponsored_cows as $sc): ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 d-flex align-items-center gap-3">
                                        <img src="<?php echo e($sc['photo']); ?>" width="60" height="60" class="rounded-circle object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                                        <div>
                                            <strong class="font-heading d-block"><?php echo e($sc['cow_name']); ?> (<?php echo e($sc['cow_code']); ?>)</strong>
                                            <small class="text-muted d-block"><?php echo e($sc['breed']); ?> Breed</small>
                                            <a href="/Kamadhenu-goushala/cow-detail.php?id=<?php echo $sc['cow_id']; ?>" class="small text-warning font-ui fw-bold">View Details &amp; Updates &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Donations -->
                <div class="kamadenu-card p-4">
                    <h4 class="font-heading mb-3"><i class="fas fa-receipt text-warning me-2"></i> My Donation History</h4>
                    <?php if (empty($user_donations)): ?>
                        <p class="text-muted">No donation history recorded.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle small">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Purpose</th>
                                        <th>Amount</th>
                                        <th>Receipt #</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_donations as $ud): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($ud['created_at'])); ?></td>
                                            <td><?php echo e($ud['purpose']); ?></td>
                                            <td class="font-mono fw-bold">₹<?php echo number_format($ud['amount']); ?></td>
                                            <td><span class="badge bg-dark font-mono"><?php echo e($ud['receipt_number']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Certificates & Badges -->
            <div class="col-lg-5">
                <!-- Digital Certificates -->
                <div class="kamadenu-card p-4 mb-4">
                    <h4 class="font-heading mb-3"><i class="fas fa-certificate text-warning me-2"></i> My Digital Certificates</h4>
                    <?php if (empty($certificates)): ?>
                        <p class="text-muted small">Certificates will automatically appear here when you make donations or sponsor cows.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($certificates as $cert): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="font-heading d-block text-dark"><?php echo e($cert['title']); ?></strong>
                                        <small class="text-muted font-mono"><?php echo e($cert['cert_code']); ?> &bull; Issued <?php echo date('M Y', strtotime($cert['issue_date'])); ?></small>
                                    </div>
                                    <a href="/Kamadhenu-goushala/certificate-verify.php?code=<?php echo e($cert['cert_code']); ?>" target="_blank" class="btn btn-sm btn-outline-warning font-ui"><i class="fas fa-qrcode"></i> Verify</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Badges -->
                <div class="kamadenu-card p-4">
                    <h4 class="font-heading mb-3"><i class="fas fa-award text-warning me-2"></i> Earned Gouseva Badges</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($badges as $bdg): ?>
                            <div class="badge bg-warning-subtle text-dark border border-warning p-2 font-ui">
                                <i class="fas <?php echo e($bdg['icon']); ?> text-warning me-1"></i> <strong><?php echo e($bdg['name']); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
