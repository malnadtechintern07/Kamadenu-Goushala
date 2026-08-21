<?php
require_once __DIR__ . '/includes/header.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'donation'; // donation, sponsorship, seva, cart

$amount = 0;
$description = 'Gouseva Contribution';
$entity_id = 0;

if ($type === 'donation') {
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 2500.00;
    $description = isset($_GET['purpose']) ? $_GET['purpose'] : 'General Gouseva Donation';
} elseif ($type === 'sponsorship') {
    $cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 1;
    $dur = isset($_GET['duration']) ? intval($_GET['duration']) : 1;
    
    $stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
    $stmt->execute([$cow_id]);
    $cow = $stmt->fetch();
    
    $monthly = $cow ? floatval($cow['monthly_sponsorship_amount']) : 2500.00;
    $amount = $monthly * $dur;
    $description = "Sponsorship of cow {$cow['name']} ({$cow['cow_code']}) for {$dur} Months";
    $entity_id = $cow_id;
} elseif ($type === 'seva') {
    $seva_id = isset($_GET['seva_id']) ? intval($_GET['seva_id']) : 1;
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 1000.00;
    
    $stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
    $stmt->execute([$seva_id]);
    $seva = $stmt->fetch();
    $description = "Seva Sponsorship: " . ($seva ? $seva['title'] : 'Gouseva');
    $entity_id = $seva_id;
} elseif ($type === 'cart') {
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0.00;
    $description = "Kamadenu Store Order Payment";
}

$donor_name = isset($_GET['name']) ? trim($_GET['name']) : ($user ? $user['name'] : 'Devotee');
$donor_email = isset($_GET['email']) ? trim($_GET['email']) : ($user ? $user['email'] : 'devotee@kamadenugoushala.org');
$donor_phone = isset($_GET['phone']) ? trim($_GET['phone']) : ($user ? $user['phone'] : '+91 99000 00000');
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><i class="fas fa-lock me-2"></i> Secure Payment Gateway</h1>
        <p class="text-white-50 mb-0">Select your preferred payment method (UPI, Cards, NetBanking, Direct Bank Transfer).</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center g-4">
            <div class="col-lg-7">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-3">Order & Contribution Summary</h3>
                    <div class="p-3 bg-secondary-subtle rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-ui fw-bold text-dark"><?php echo e($description); ?></span>
                            <span class="badge bg-warning text-dark font-mono fs-6">₹<?php echo number_format($amount, 2); ?></span>
                        </div>
                        <small class="text-muted d-block">Payer: <?php echo e($donor_name); ?> (<?php echo e($donor_email); ?>)</small>
                    </div>

                    <!-- Payment Method Selection Tabs -->
                    <h4 class="font-heading fs-5 mb-3"><i class="fas fa-credit-card me-2 text-warning"></i> Select Payment Method</h4>

                    <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                        <input type="hidden" name="type" value="<?php echo e($type); ?>">
                        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                        <input type="hidden" name="entity_id" value="<?php echo $entity_id; ?>">
                        <input type="hidden" name="donor_name" value="<?php echo e($donor_name); ?>">
                        <input type="hidden" name="donor_email" value="<?php echo e($donor_email); ?>">
                        <input type="hidden" name="donor_phone" value="<?php echo e($donor_phone); ?>">

                        <div class="accordion mb-4" id="paymentOptionsAccordion">
                            <!-- Option 1: UPI Payment (Google Pay / PhonePe / Paytm) -->
                            <div class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optUPI" checked>
                                        <i class="fas fa-mobile-alt me-2 text-success"></i> Instant UPI / QR Code (Google Pay, PhonePe, Paytm, BHIM)
                                    </button>
                                </h2>
                                <div id="optUPI" class="accordion-collapse collapse show" data-bs-parent="#paymentOptionsAccordion">
                                    <div class="accordion-body text-start">
                                        <div class="text-center p-3 bg-light rounded mb-3">
                                            <i class="fas fa-qrcode display-4 text-dark mb-2"></i>
                                            <div class="font-mono fw-bold text-dark">UPI ID: kamadenu@upi</div>
                                            <small class="text-muted">Scan QR or transfer to official Goushala UPI ID</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label font-ui small fw-bold">Enter Your UPI ID (e.g. user@okaxis)</label>
                                            <input type="text" name="upi_id" class="form-control" placeholder="yourname@upi">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Razorpay Sandbox Cards & NetBanking -->
                            <div class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optCard">
                                        <i class="fas fa-credit-card me-2 text-warning"></i> Credit / Debit Cards & NetBanking (Razorpay)
                                    </button>
                                </h2>
                                <div id="optCard" class="accordion-collapse collapse" data-bs-parent="#paymentOptionsAccordion">
                                    <div class="accordion-body text-start">
                                        <p class="small text-muted">Supports Visa, MasterCard, RuPay, SBI, HDFC, ICICI NetBanking.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 3: Direct Bank Transfer (NEFT / RTGS) -->
                            <div class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optBank">
                                        <i class="fas fa-university me-2 text-primary"></i> Direct Bank Transfer (NEFT / RTGS / IMPS)
                                    </button>
                                </h2>
                                <div id="optBank" class="accordion-collapse collapse" data-bs-parent="#paymentOptionsAccordion">
                                    <div class="accordion-body text-start">
                                        <div class="small bg-light p-3 rounded mb-3 font-mono">
                                            <div><strong>Bank Name:</strong> State Bank of India</div>
                                            <div><strong>Account Name:</strong> Kamadenu Goushala Trust</div>
                                            <div><strong>Account Number:</strong> 398200119283</div>
                                            <div><strong>IFSC Code:</strong> SBIN0004291</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label font-ui small fw-bold">Transaction Reference / UTR Number</label>
                                            <input type="text" name="utr_number" class="form-control" placeholder="e.g. UTR2938102938">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow">
                            <i class="fas fa-check-circle me-2"></i> Complete Payment & Update Admin Panel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function handlePaymentSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);

    const payload = {
        razorpay_payment_id: 'pay_' + Math.random().toString(36).substring(2, 12),
        razorpay_order_id: 'order_' + Math.random().toString(36).substring(2, 12),
        razorpay_signature: 'simulated_sig_' + Date.now(),
        entity_type: formData.get('type') === 'sponsorship' ? 'Sponsorship' : (formData.get('type') === 'seva' ? 'Seva' : (formData.get('type') === 'cart' ? 'Order' : 'Donation')),
        entity_id: formData.get('entity_id'),
        amount: formData.get('amount'),
        donor_name: formData.get('donor_name'),
        donor_email: formData.get('donor_email'),
        donor_phone: formData.get('donor_phone'),
        purpose: '<?php echo addslashes($description); ?>'
    };

    fetch('/Kamadenu/api/payments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (formData.get('type') === 'cart') {
                localStorage.removeItem('kamadenu_cart');
            }
            window.location.href = '/Kamadenu/thank-you.php?payment_id=' + data.payment_id + '&receipt=' + data.receipt_number + '&amount=' + formData.get('amount');
        } else {
            alert('Payment processing error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Payment verification failed.');
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
