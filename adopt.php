<?php
require_once __DIR__ . '/includes/header.php';

$selected_cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 0;
$cows = $pdo->query("SELECT c.*, wn.phone_number as wa_phone_dir FROM cows c LEFT JOIN whatsapp_numbers wn ON c.whatsapp_number_id = wn.id ORDER BY c.id ASC")->fetchAll();
$default_whatsapp_adoption = get_setting($pdo, 'whatsapp_adoption_default', '+91 98800 12345');
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

                    <form id="adoptForm" onsubmit="handleAdoptSubmit(event)">
                        <input type="hidden" name="type" value="sponsorship">

                        <!-- Select Cow -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Select Indigenous Cow</label>
                            <select name="cow_id" class="form-select form-select-lg" required>
                                <option value="">-- Select Cow --</option>
                                <?php foreach ($cows as $c): ?>
                                    <?php 
                                        $cname = __td($c, 'name'); 
                                        $cwa = !empty($c['wa_phone_dir']) ? $c['wa_phone_dir'] : $default_whatsapp_adoption;
                                    ?>
                                    <option value="<?php echo $c['id']; ?>" data-price="<?php echo $c['monthly_sponsorship_amount']; ?>" data-whatsapp="<?php echo e($cwa); ?>" data-method="<?php echo e($c['contact_method']); ?>" data-message="<?php echo e($c['whatsapp_message']); ?>" <?php echo $selected_cow_id === $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($c['cow_code']); ?> - <?php echo e($cname); ?> (<?php echo e($c['breed']); ?>) - ₹<?php echo number_format($c['monthly_sponsorship_amount']); ?>/month [<?php echo $c['adoption_status'] === 'Sponsored' ? __t('status_sponsored') : __t('status_available'); ?>]
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

                        <button type="submit" id="adoptSubmitBtn" onclick="currentAction = 'website'" class="btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow">
                            <i class="fas fa-heart me-2"></i> Select Cow to Sponsor
                        </button>
                        <button type="submit" id="adoptWaSubmitBtn" onclick="currentAction = 'whatsapp'" class="btn btn-success w-100 py-3 font-ui fs-5 fw-bold shadow mt-3" style="display: none;">
                            <i class="fab fa-whatsapp me-2"></i> Confirm & Adopt via WhatsApp
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let currentAction = 'website';

document.addEventListener("DOMContentLoaded", function() {
    const cowSelect = document.querySelector('select[name="cow_id"]');
    const submitBtn = document.getElementById('adoptSubmitBtn');
    const waSubmitBtn = document.getElementById('adoptWaSubmitBtn');
 
    function updateSubmitButton() {
        if (!cowSelect || !submitBtn || !waSubmitBtn) return;
        const selectedOption = cowSelect.options[cowSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            submitBtn.innerHTML = '<i class="fas fa-heart me-2"></i> Select Cow to Sponsor';
            submitBtn.className = 'btn btn-secondary w-100 py-3 font-ui fs-5 fw-bold shadow';
            submitBtn.style.display = 'block';
            waSubmitBtn.style.display = 'none';
            return;
        }
        const method = selectedOption.getAttribute('data-method') || 'website';
        if (method === 'whatsapp') {
            submitBtn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Confirm & Adopt via WhatsApp';
            submitBtn.className = 'btn btn-success w-100 py-3 font-ui fs-5 fw-bold shadow';
            submitBtn.style.display = 'block';
            waSubmitBtn.style.display = 'none';
        } else if (method === 'both') {
            submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i> Proceed to Secure Checkout';
            submitBtn.className = 'btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow';
            submitBtn.style.display = 'block';
            
            waSubmitBtn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Confirm & Adopt via WhatsApp';
            waSubmitBtn.className = 'btn btn-success w-100 py-3 font-ui fs-5 fw-bold shadow';
            waSubmitBtn.style.display = 'block';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i> Proceed to Secure Checkout';
            submitBtn.className = 'btn btn-kamadenu-primary w-100 py-3 font-ui fs-5 fw-bold shadow';
            submitBtn.style.display = 'block';
            waSubmitBtn.style.display = 'none';
        }
    }
 
    if (cowSelect) {
        cowSelect.addEventListener('change', updateSubmitButton);
        updateSubmitButton();
    }
});

function handleAdoptSubmit(e) {
    const form = e.target;
    const cowSelect = form.querySelector('select[name="cow_id"]');
    const selectedOption = cowSelect.options[cowSelect.selectedIndex];
    const contactMethod = selectedOption.getAttribute('data-method') || 'website';

    let action = contactMethod;
    if (contactMethod === 'both') {
        action = currentAction;
    }

    if (action === 'website') {
        form.action = '/Kamadenu/checkout.php';
        form.method = 'GET';
        return; // Submit naturally
    }

    e.preventDefault();
    const submitBtn = document.getElementById('adoptSubmitBtn');
    const waSubmitBtn = document.getElementById('adoptWaSubmitBtn');
    
    submitBtn.disabled = true;
    waSubmitBtn.disabled = true;
    
    const originalText = submitBtn.innerHTML;
    const originalWaText = waSubmitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
    waSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';

    const cowId = cowSelect.value;
    const cowText = selectedOption.text;
    const monthlyPrice = parseFloat(selectedOption.getAttribute('data-price') || 0);
    const whatsappNum = selectedOption.getAttribute('data-whatsapp');

    const duration = parseInt(form.querySelector('input[name="duration"]:checked').value);
    const name = form.querySelector('input[name="name"]').value;
    const email = form.querySelector('input[name="email"]').value;
    const phone = form.querySelector('input[name="phone"]').value;
    const pan = form.querySelector('input[name="pan"]').value;

    const totalAmount = monthlyPrice * duration;

    const payload = {
        action: 'verify_payment',
        payment_method: 'WhatsApp',
        entity_type: 'Sponsorship',
        cow_id: cowId,
        amount: totalAmount,
        duration_months: duration,
        sponsor_name: name,
        sponsor_email: email,
        sponsor_phone: phone,
        pan_number: pan
    };

    fetch('/Kamadenu/api/payments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            let msg = '';
            const customMsg = selectedOption.getAttribute('data-message');
            if (customMsg && customMsg.trim() !== '') {
                msg = customMsg.trim() + `\n\n*(Reference: ${data.data.payment_id})*`;
            } else {
                msg = `Hare Krishna! I would like to adopt a cow at Kamadenu Goushala.\n\n`;
                msg += `*Cow Details*:\n`;
                msg += `- Cow: ${cowText.trim()}\n`;
                msg += `- Monthly Cost: ₹${monthlyPrice.toLocaleString('en-IN')}\n`;
                msg += `- Duration: ${duration} Month(s)\n`;
                msg += `- Total Commitment: ₹${totalAmount.toLocaleString('en-IN')}\n\n`;
                msg += `*Sponsor Details*:\n`;
                msg += `- Name: ${name}\n`;
                msg += `- Email: ${email}\n`;
                msg += `- Phone: ${phone}\n`;
                if (pan) {
                    msg += `- PAN: ${pan}\n`;
                }
                msg += `\n_Please confirm my sponsorship. (Reference: ${data.data.payment_id})_`;
            }

            const cleanPhone = whatsappNum.replace(/[^0-9]/g, '');
            const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
            
            window.location.href = waUrl;
        } else {
            alert('Error generating adoption request: ' + data.message);
            submitBtn.disabled = false;
            waSubmitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            waSubmitBtn.innerHTML = originalWaText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Verification request failed.');
        submitBtn.disabled = false;
        waSubmitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        waSubmitBtn.innerHTML = originalWaText;
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
