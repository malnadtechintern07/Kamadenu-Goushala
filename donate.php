<?php
require_once __DIR__ . '/includes/header.php';

$campaign_id = isset($_GET['campaign']) ? intval($_GET['campaign']) : 0;
$campaign = null;
if ($campaign_id > 0) {
    $stmt = $pdo->prepare("SELECT ec.*, wn.phone_number as wa_phone_dir FROM emergency_campaigns ec LEFT JOIN whatsapp_numbers wn ON ec.whatsapp_number_id = wn.id WHERE ec.id = ?");
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

                        <?php
                        $resolved_method = 'website';
                        if ($campaign) {
                            $resolved_method = $campaign['contact_method'];
                        } else {
                            $resolved_method = get_setting($pdo, 'donation_action_mode', 'website');
                        }

                        $enabled_methods = [];
                        if ($resolved_method === 'website') {
                            $enabled_methods = ['website'];
                        } elseif ($resolved_method === 'whatsapp') {
                            $enabled_methods = ['whatsapp'];
                        } elseif ($resolved_method === 'qrcode') {
                            $enabled_methods = ['qrcode'];
                        } elseif ($resolved_method === 'both') {
                            $enabled_methods = ['website', 'whatsapp'];
                        } elseif ($resolved_method === 'website_qrcode') {
                            $enabled_methods = ['website', 'qrcode'];
                        } elseif ($resolved_method === 'whatsapp_qrcode') {
                            $enabled_methods = ['whatsapp', 'qrcode'];
                        } elseif ($resolved_method === 'all') {
                            $enabled_methods = ['website', 'whatsapp', 'qrcode'];
                        } else {
                            $enabled_methods = ['website']; // fallback
                        }
                        ?>

                        <!-- Choose Donation Payment Option -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold d-block text-center mb-3">Choose Donation Payment Option</label>
                            <div class="row g-3">
                                <?php if (in_array('website', $enabled_methods)): ?>
                                <div class="col-sm">
                                    <input type="radio" class="btn-check" name="payment_option" id="pay_opt_web" value="website" checked onclick="togglePaymentView('website')">
                                    <label class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4" for="pay_opt_web">
                                        <i class="fas fa-globe fs-3"></i>
                                        <span class="fw-bold">Website Payment</span>
                                    </label>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (in_array('whatsapp', $enabled_methods)): ?>
                                <div class="col-sm">
                                    <input type="radio" class="btn-check" name="payment_option" id="pay_opt_wa" value="whatsapp" <?php echo !in_array('website', $enabled_methods) ? 'checked' : ''; ?> onclick="togglePaymentView('whatsapp')">
                                    <label class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4" for="pay_opt_wa">
                                        <i class="fab fa-whatsapp fs-3"></i>
                                        <span class="fw-bold">WhatsApp Payment</span>
                                    </label>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('qrcode', $enabled_methods)): ?>
                                <div class="col-sm">
                                    <input type="radio" class="btn-check" name="payment_option" id="pay_opt_qr" value="qrcode" <?php echo (!in_array('website', $enabled_methods) && !in_array('whatsapp', $enabled_methods)) ? 'checked' : ''; ?> onclick="togglePaymentView('qrcode')">
                                    <label class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4" for="pay_opt_qr">
                                        <i class="fas fa-qrcode fs-3"></i>
                                        <span class="fw-bold">QR Code Payment</span>
                                    </label>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Dynamic Content Views -->
                        <div id="payment-view-website" class="payment-view-div" style="display: none;">
                            <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow rounded-pill">
                                <i class="fas fa-lock me-2"></i> Proceed to Secure Razorpay Donation
                            </button>
                        </div>

                        <div id="payment-view-whatsapp" class="payment-view-div" style="display: none;">
                            <button type="button" onclick="submitDonationToWhatsApp(event)" class="btn btn-success w-100 py-3 font-ui fs-5 fw-bold shadow rounded-pill">
                                <i class="fab fa-whatsapp me-2"></i> Confirm &amp; Donate via WhatsApp
                            </button>
                        </div>

                        <div id="payment-view-qrcode" class="payment-view-div" style="display: none;">
                            <div class="text-center p-4 bg-black bg-opacity-25 rounded-4 border border-warning border-opacity-10 mb-4">
                                <h5 class="font-heading text-warning mb-3">Scan QR Code to Pay</h5>
                                
                                <?php 
                                $qr_code_setting = get_setting($pdo, 'donation_qr_code', 'assets/images/donation_qr.png');
                                $upi_id_setting = get_setting($pdo, 'donation_upi_id', 'kamadenu@upi');
                                ?>
                                
                                <img src="<?php echo htmlspecialchars(img_url($qr_code_setting)); ?>" alt="Donation QR Code" class="img-fluid mb-3 rounded-3 shadow-sm border border-secondary" style="max-height: 220px; width: auto;">
                                
                                <div class="mb-3">
                                    <span class="d-block text-white-50 small mb-1">Official Goushala UPI ID</span>
                                    <div class="d-inline-flex align-items-center gap-2 bg-dark px-3 py-2 rounded-pill border border-secondary border-opacity-25">
                                        <code class="text-warning font-mono fs-6" id="upi-id-code"><?php echo e($upi_id_setting); ?></code>
                                        <button type="button" class="btn btn-link text-white-50 p-0 fs-7 border-0 bg-transparent" onclick="copyUpiId()" title="Copy UPI ID"><i class="far fa-copy"></i></button>
                                    </div>
                                </div>
                                <p class="text-white-50 small mb-0">Use any UPI app (BHIM, Google Pay, PhonePe, Paytm) to scan and pay.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-ui small fw-bold">Transaction Reference / UTR Number</label>
                                <input type="text" name="qr_utr" id="qr_utr_input" class="form-control form-control-lg font-mono text-center" placeholder="e.g. 392810293810">
                                <small class="text-white-50 d-block mt-1">Please enter the 12-digit transaction ID or UTR number after scanning and making the payment.</small>
                            </div>

                            <button type="button" onclick="submitDonationToQRCode(event)" class="btn btn-info w-100 py-3 font-ui fs-5 fw-bold text-dark shadow rounded-pill">
                                <i class="fas fa-check-circle me-2"></i> Submit QR Payment Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function togglePaymentView(method) {
    document.querySelectorAll('.payment-view-div').forEach(el => el.style.display = 'none');
    const viewEl = document.getElementById('payment-view-' + method);
    if (viewEl) {
        viewEl.style.display = 'block';
    }
}

function copyUpiId() {
    const codeEl = document.getElementById('upi-id-code');
    if (codeEl) {
        navigator.clipboard.writeText(codeEl.textContent.trim()).then(() => {
            alert('UPI ID copied to clipboard: ' + codeEl.textContent.trim());
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
}

function submitDonationToWhatsApp(e) {
    e.preventDefault();
    const amount = document.getElementById('custom_amt').value;
    const purpose = document.querySelector('select[name="purpose"]').value;
    const name = document.querySelector('input[name="name"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const phone = document.querySelector('input[name="phone"]').value;
    const pan = document.querySelector('input[name="pan"]').value;
    const isAnonymous = document.getElementById('anonCheck').checked ? 'Yes' : 'No';
    
    let waPhone = <?php echo json_encode($campaign ? $campaign['wa_phone_dir'] : get_setting($pdo, 'whatsapp_donation_default', '+91 98800 12345')); ?>;
    if (!waPhone) {
        waPhone = '+91 98800 12345';
    }
    
    let msg = `Hare Krishna! I would like to make a Gouseva contribution to Kamadenu Goushala.\n\n`;
    msg += `*Contribution Details*:\n`;
    msg += `- Purpose: ${purpose}\n`;
    msg += `- Amount: ₹${parseFloat(amount).toLocaleString('en-IN')}\n`;
    msg += `- Anonymous: ${isAnonymous}\n\n`;
    msg += `*Donor Details*:\n`;
    msg += `- Name: ${name}\n`;
    msg += `- Phone: ${phone}\n`;
    msg += `- Email: ${email}\n`;
    if (pan.trim() !== '') {
        msg += `- PAN: ${pan.trim()}\n`;
    }
    
    const cleanPhone = waPhone.replace(/[^0-9]/g, '');
    const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
    window.open(waUrl, '_blank');
}

function submitDonationToQRCode(e) {
    e.preventDefault();
    
    const amount = document.getElementById('custom_amt').value;
    const purpose = document.querySelector('select[name="purpose"]').value;
    const name = document.querySelector('input[name="name"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const phone = document.querySelector('input[name="phone"]').value;
    const pan = document.querySelector('input[name="pan"]').value;
    const isAnonymous = document.getElementById('anonCheck').checked ? 1 : 0;
    const utr = document.getElementById('qr_utr_input').value.trim();
    
    if (!amount || amount < 10) {
        alert('Please enter a valid amount.');
        return;
    }
    if (!name || !email || !phone) {
        alert('Please fill in your name, email, and phone number.');
        return;
    }
    if (!utr) {
        alert('Please enter the Transaction Reference / UTR Number.');
        return;
    }
    
    const payload = {
        action: 'verify_payment',
        payment_method: 'QR Code',
        payment_id: 'UTR-' + utr,
        order_id: 'manual_' + Math.random().toString(36).substring(2, 12),
        signature: 'manual_sig_' + Date.now(),
        entity_type: 'Donation',
        amount: amount,
        donor_name: name,
        donor_email: email,
        donor_phone: phone,
        pan: pan,
        is_anonymous: isAnonymous,
        purpose: purpose,
        campaign_id: <?php echo json_encode($campaign_id); ?>
    };
    
    const submitBtn = e.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting payment info...';
    
    fetch('/Kamadenu/api/payments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            const pId = data.payment_id || ('UTR-' + utr);
            const rcpt = data.receipt_number || '';
            window.location.href = '/Kamadenu/thank-you.php?payment_id=' + encodeURIComponent(pId) + '&receipt=' + encodeURIComponent(rcpt) + '&amount=' + encodeURIComponent(amount);
        } else {
            alert('Error submitting payment details: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Verification submission failed. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const checkedOpt = document.querySelector('input[name="payment_option"]:checked');
    if (checkedOpt) {
        togglePaymentView(checkedOpt.value);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
