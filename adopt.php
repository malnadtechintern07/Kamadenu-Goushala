<?php
require_once __DIR__ . '/includes/header.php';

$selected_cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 0;
$cows = $pdo->query("SELECT * FROM cows ORDER BY id ASC")->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_adopt'); ?></h1>
                <p class="text-white-50 mb-0">Sponsor monthly fodder, Ayurvedic healthcare, and shelter for an indigenous cow.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4 text-center">Select Cow & Sponsorship Plan</h3>

                    <form action="/Kamadenu/checkout.php" method="GET">
                        <input type="hidden" name="type" value="sponsorship">

                        <!-- Select Cow -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Select Indigenous Cow</label>
                            <select name="cow_id" class="form-select form-select-lg" required>
                                <option value="">-- Select Cow --</option>
                                <?php foreach ($cows as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $selected_cow_id === $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($c['cow_code']); ?> - <?php echo e($c['name']); ?> (<?php echo e($c['breed']); ?>) - ₹<?php echo number_format($c['monthly_sponsorship_amount']); ?>/month [<?php echo e($c['adoption_status']); ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Duration Selector -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Sponsorship Duration</label>
                            <div class="row g-3 text-center">
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="duration" id="dur1" value="1" checked>
                                    <label class="btn btn-outline-warning w-100 py-3 font-ui fw-bold" for="dur1">1 Month</label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="duration" id="dur3" value="3">
                                    <label class="btn btn-outline-warning w-100 py-3 font-ui fw-bold" for="dur3">3 Months</label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="duration" id="dur6" value="6">
                                    <label class="btn btn-outline-warning w-100 py-3 font-ui fw-bold" for="dur6">6 Months</label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="duration" id="dur12" value="12">
                                    <label class="btn btn-outline-warning w-100 py-3 font-ui fw-bold" for="dur12">1 Year</label>
                                </div>
                            </div>
                        </div>

                        <!-- Sponsor Details -->
                        <h5 class="font-heading border-top pt-4 mb-3">Sponsor Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Your Name" value="<?php echo $user ? e($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Email for Updates & Receipt" value="<?php echo $user ? e($user['email']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 Phone Number" value="<?php echo $user ? e($user['phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">PAN Card Number (for 80G Tax Receipt)</label>
                                <input type="text" name="pan" class="form-control" placeholder="e.g. ABCDE1234F">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow">
                            <i class="fas fa-hand-holding-heart me-2"></i> Proceed to Cow Adoption Checkout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
