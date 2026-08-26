<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php?redirect=" . urlencode('/Kamadenu/my-cows.php') . "&msg=login_required"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT s.*, c.name as cow_name, c.cow_code, c.breed, c.photo, c.health_status FROM sponsorships s JOIN cows c ON s.cow_id = c.id JOIN sponsors sp ON s.sponsor_id = sp.id WHERE sp.user_id = ?");
$stmt->execute([$user['id']]);
$sponsored_cows = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Sponsored & Adopted Cows</h1>
        <p class="text-white-50 mb-0">Track monthly updates and health passports for your sponsored cattle.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($sponsored_cows)): ?>
            <div class="kamadenu-card p-5 text-center">
                <i class="fas fa-cow fs-1 text-muted mb-3 d-block"></i>
                <h4 class="font-heading">No Sponsored Cows Yet</h4>
                <p class="text-muted">You have not adopted or sponsored any cows. Help support indigenous cows today!</p>
                <a href="/Kamadenu/adopt.php" class="btn btn-warning font-ui fw-bold px-4 py-2 mt-2">Sponsor a Cow</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($sponsored_cows as $sc): ?>
                    <div class="col-md-6">
                        <div class="kamadenu-card p-4 d-flex align-items-center gap-4">
                            <img src="/Kamadenu/<?php echo e($sc['photo']); ?>" class="rounded-circle shadow" style="width: 90px; height: 90px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                            <div>
                                <span class="badge-cow-code mb-1"><?php echo e($sc['cow_code']); ?></span>
                                <h3 class="font-heading mb-1"><?php echo e($sc['cow_name']); ?></h3>
                                <p class="small text-muted mb-2">
                                    <?php echo e($sc['breed']); ?> Breed &bull; Status: 
                                    <?php if ($sc['status'] === 'Active'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Payment Complete</span>
                                    <?php elseif ($sc['status'] === 'Pending Approval'): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> <?php echo e($sc['status']); ?></span>
                                    <?php endif; ?>
                                </p>
                                <a href="/Kamadenu/cow-detail.php?id=<?php echo $sc['cow_id']; ?>" class="btn btn-sm btn-kamadenu-primary font-ui fw-bold">View Cattle Profile &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
