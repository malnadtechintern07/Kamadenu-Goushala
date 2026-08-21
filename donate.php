<?php
require_once __DIR__ . '/includes/header.php';

$campaign_id = isset($_GET['campaign']) ? intval($_GET['campaign']) : 0;
$campaign = null;
if ($campaign_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM emergency_campaigns WHERE id = ?");
    $stmt->execute([$campaign_id]);
    $campaign = $stmt->fetch();
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_donate'); ?></h1>
                <p class="text-white-50 mb-0">Every donation goes directly towards feeding, sheltering, and treating rescued indigenous cattle. Eligible for 80G Tax Exemption.</p>
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
                <?php if ($campaign): ?>
                    <div class="alert bg-warning-subtle border border-warning text-dark p-3 rounded-4 mb-4">
                        <h4 class="font-heading text-danger mb-1"><i class="fas fa-bullhorn me-2"></i> Supporting Emergency Relief: <?php echo e($campaign['title']); ?></h4>
                        <p class="small mb-0"><?php echo e($campaign['story']); ?></p>
                    </div>
                <?php endif; ?>

                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4 text-center">Make a Devotional Contribution</h3>

                    <form action="/Kamadenu/checkout.php" method="GET">
                        <input type="hidden" name="type" value="donation">
                        <?php if ($campaign): ?>
                            <input type="hidden" name="campaign_id" value="<?php echo $campaign['id']; ?>">
                        <?php endif; ?>

                        <!-- Donation Amount Tiers -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Select Donation Amount</label>
                            <div class="row g-3 text-center mb-3">
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amt500" value="500" onclick="document.getElementById('custom_amt').value='500'">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amt500">₹500</label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amt1000" value="1000" onclick="document.getElementById('custom_amt').value='1000'">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amt1000">₹1,000</label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amt2500" value="2500" checked onclick="document.getElementById('custom_amt').value='2500'">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amt2500">₹2,500</label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amt5000" value="5000" onclick="document.getElementById('custom_amt').value='5000'">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amt5000">₹5,000</label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amt10000" value="10000" onclick="document.getElementById('custom_amt').value='10000'">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amt10000">₹10,000</label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="tier_amount" id="amtcustom" value="custom">
                                    <label class="btn btn-outline-warning w-100 py-3 font-mono fw-bold" for="amtcustom">Custom</label>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-text font-mono fw-bold fs-5 bg-white">₹</span>
                                <input type="number" name="amount" id="custom_amt" class="form-control form-control-lg font-mono fw-bold" value="2500" min="10" required placeholder="Enter donation amount in INR">
                            </div>
                        </div>

                        <!-- Donation Purpose -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Donation Purpose</label>
                            <select name="purpose" class="form-select form-select-lg">
                                <option value="General Gouseva" <?php echo !$campaign ? 'selected' : ''; ?>>General Gouseva & Maintenance</option>
                                <option value="Green Fodder & Grain Feed">Green Fodder & Organic Grain Feed</option>
                                <option value="Emergency Rescue Campaign" <?php echo $campaign ? 'selected' : ''; ?>>Emergency Medical & Rescue Campaign</option>
                                <option value="Veterinary Medicines & Surgery">Veterinary Medicines & Surgery</option>
                                <option value="Goushala Shelter Infrastructure">Goushala Shelter Infrastructure</option>
                            </select>
                        </div>

                        <!-- Donor Details -->
                        <h5 class="font-heading border-top pt-4 mb-3">Donor Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Donor Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Full Name" value="<?php echo $user ? e($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Email Address for Receipt" value="<?php echo $user ? e($user['email']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 Phone Number" value="<?php echo $user ? e($user['phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">PAN Card Number (for 80G Tax Exemption)</label>
                                <input type="text" name="pan" class="form-control" placeholder="e.g. ABCDE1234F">
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="is_anonymous" value="1" id="anonCheck">
                            <label class="form-check-label font-ui small" for="anonCheck">
                                Keep my donation anonymous on public donor lists
                            </label>
                        </div>

                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow">
                            <i class="fas fa-lock me-2"></i> Proceed to Secure Razorpay Donation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
