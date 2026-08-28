<?php
require_once __DIR__ . '/includes/header.php';

if (!is_user_logged_in()) {
    $redirect_url = $_SERVER['REQUEST_URI'];
    header("Location: /Kamadhenu-goushala/login.php?redirect=" . urlencode($redirect_url) . "&msg=login_required");
    exit;
}

$selected_cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 0;

// Fetch all available feeding cows
$cows = $pdo->query("SELECT fc.*, wn.phone_number as wa_phone_dir FROM feeding_cows fc LEFT JOIN whatsapp_numbers wn ON fc.whatsapp_number_id = wn.id WHERE fc.is_available = 1 ORDER BY fc.id ASC")->fetchAll();
$default_whatsapp_adoption = get_setting($pdo, 'whatsapp_adoption_default', '+91 98800 12345');

// Validate selected cow ID against database and resolve cows <-> feeding_cows mapping
if ($selected_cow_id > 0) {
    $matched_fc_id = 0;
    foreach ($cows as $fc_item) {
        if ($fc_item['id'] === $selected_cow_id) {
            $matched_fc_id = $selected_cow_id;
            break;
        }
    }
    
    // If not directly matching feeding_cows.id, check cows table by ID and match by name/code
    if (!$matched_fc_id) {
        $stmt_cow = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
        $stmt_cow->execute([$selected_cow_id]);
        $target_cow = $stmt_cow->fetch();
        if ($target_cow) {
            foreach ($cows as $fc_item) {
                if (strtolower($fc_item['name']) === strtolower($target_cow['name']) || $fc_item['cow_code'] === $target_cow['cow_code']) {
                    $matched_fc_id = $fc_item['id'];
                    break;
                }
            }
            // If still no feeding_cows record exists, dynamically append target cow to list
            if (!$matched_fc_id) {
                $dynamic_item = [
                    'id' => $target_cow['id'],
                    'cow_code' => $target_cow['cow_code'],
                    'name' => $target_cow['name'],
                    'description' => $target_cow['rescue_story'],
                    'photo' => $target_cow['photo'],
                    'feed_amount' => $target_cow['monthly_sponsorship_amount'] > 0 ? min($target_cow['monthly_sponsorship_amount'], 1000) : 500.00,
                    'payment_method' => 'both',
                    'wa_phone_dir' => $default_whatsapp_adoption,
                    'whatsapp_message' => ''
                ];
                $cows[] = $dynamic_item;
                $matched_fc_id = $target_cow['id'];
            }
        }
    }
    if ($matched_fc_id) {
        $selected_cow_id = $matched_fc_id;
    }
}

// Prepare cows list as JSON for client-side dynamic updates
$cows_json = [];
foreach ($cows as $c) {
    $cows_json[$c['id']] = [
        'id' => $c['id'],
        'cow_code' => $c['cow_code'],
        'name' => $c['name'],
        'description' => $c['description'],
        'photo' => img_url($c['photo']),
        'feed_amount' => floatval($c['feed_amount']),
        'payment_method' => $c['payment_method'],
        'whatsapp_phone' => !empty($c['wa_phone_dir']) ? $c['wa_phone_dir'] : $default_whatsapp_adoption,
        'whatsapp_message' => $c['whatsapp_message'] ?? ''
    ];
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><i class="fas fa-cookie-bite me-2"></i> Feed a Sacred Cow</h1>
                <p class="text-white-50 mb-0">Directly feed individual rescued indigenous cows at our sanctuary with fresh grass, feed mash, and medical feasts.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Column: Dynamic Cow Passport Display -->
            <div class="col-lg-5">
                <div class="kamadenu-card p-4 text-center sticky-top" style="top: 100px;" id="cow-details-card">
                    <!-- Default Placeholder / Display Container -->
                    <div id="cow-details-content">
                        <img id="cow-photo" src="https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80" class="img-fluid rounded-4 shadow mb-4 hover-glow" style="max-height: 280px; object-fit: cover; width: 100%;">
                        <h3 class="font-heading mb-1" id="cow-name">Select a Cow</h3>
                        <p class="text-warning font-ui fw-bold mb-3" id="cow-code-display">Official Passport ID: --</p>
                        <p class="text-muted small text-start border-top pt-3" id="cow-description">Please choose a cow from the dropdown list to see their rescue details and suggested feeding values.</p>
                        
                        <div class="row g-2 text-start small border-top pt-3 mt-3">
                            <div class="col-12">
                                <span class="text-muted d-block">Suggested Feed Contribution</span>
                                <strong class="font-ui fs-5 text-warning font-mono" id="cow-suggested-amount">₹ 500.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Feeding Form Options -->
            <div class="col-lg-7">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4"><i class="fas fa-hand-holding-heart text-warning me-2"></i> Feed Contribution Form</h3>
                    
                    <form id="feedCowForm" onsubmit="handleFeedingSubmit(event)">
                        <!-- Cow Selection Dropdown -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Selected Cow to Feed</label>
                            <select name="cow_id" class="form-select form-select-lg border-warning" required onchange="onCowSelectChanged(this.value)">
                                <option value="">-- Select Cow --</option>
                                <?php foreach ($cows as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $selected_cow_id === $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($c['cow_code']); ?> - <?php echo e($c['name']); ?> (Suggested: ₹<?php echo number_format($c['feed_amount']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Contribution Amount Picker -->
                        <div class="mb-4">
                            <label class="form-label font-ui fw-bold">Feed Contribution Amount (₹)</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text font-mono fw-bold">₹</span>
                                <input type="number" name="amount" id="custom-amount-input" class="form-control form-control-lg font-mono fw-bold text-dark" placeholder="Enter amount" min="10" required>
                            </div>
                            <!-- Quick Select Buttons -->
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-feed-cow-outline font-mono fw-bold rounded-pill px-3 py-1.5" onclick="setQuickAmount(100)">+ ₹100</button>
                                <button type="button" class="btn btn-feed-cow-outline font-mono fw-bold rounded-pill px-3 py-1.5" onclick="setQuickAmount(250)">+ ₹250</button>
                                <button type="button" class="btn btn-feed-cow-outline font-mono fw-bold rounded-pill px-3 py-1.5" onclick="setQuickAmount(500)">+ ₹500</button>
                                <button type="button" class="btn btn-feed-cow-outline font-mono fw-bold rounded-pill px-3 py-1.5" onclick="setQuickAmount(1000)">+ ₹1,000</button>
                                <button type="button" class="btn btn-feed-cow-outline font-mono fw-bold rounded-pill px-3 py-1.5" onclick="resetSuggestedAmount()">Use Suggested</button>
                            </div>
                        </div>

                        <!-- Donor Information -->
                        <h5 class="font-heading border-top pt-4 mb-3">Donor Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Your Name" value="<?php echo $user ? e($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Email for Receipt & Updates" value="<?php echo $user ? e($user['email']) : ''; ?>" required>
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

                        <!-- Payment Actions -->
                        <div id="payment-buttons-container" class="mt-4">
                            <button type="submit" id="feed-website-btn" class="btn btn-feed-cow w-100 py-3 font-ui fs-5 fw-bold shadow mb-2">
                                <i class="fas fa-lock me-2"></i> Proceed to Feed Payment
                            </button>
                            <button type="button" id="feed-whatsapp-btn" onclick="submitFeedingWhatsApp()" class="btn btn-success w-100 py-3 font-ui fs-5 fw-bold shadow" style="display: none;">
                                <i class="fab fa-whatsapp me-2"></i> Feed Cow via WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const cowsData = <?php echo json_encode($cows_json); ?>;

function onCowSelectChanged(cowId) {
    const cow = cowsData[cowId];
    if (!cow) {
        document.getElementById('cow-photo').src = "https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80";
        document.getElementById('cow-name').textContent = "Select a Cow";
        document.getElementById('cow-code-display').textContent = "Official Passport ID: --";
        document.getElementById('cow-description').textContent = "Please choose a cow from the list to view their rescue chronicle.";
        document.getElementById('cow-suggested-amount').textContent = "₹ 500.00";
        document.getElementById('custom-amount-input').value = 500;
        
        document.getElementById('feed-website-btn').style.display = 'block';
        document.getElementById('feed-whatsapp-btn').style.display = 'none';
        return;
    }

    document.getElementById('cow-photo').src = cow.photo;
    document.getElementById('cow-name').textContent = cow.name;
    document.getElementById('cow-code-display').textContent = `Official Passport ID: ${cow.cow_code}`;
    document.getElementById('cow-description').textContent = cow.description || "Rescued indigenous cow residing safely at Kamadenu Goushala Trust Sanctuary.";
    document.getElementById('cow-suggested-amount').textContent = `₹ ${cow.feed_amount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    
    document.getElementById('custom-amount-input').value = cow.feed_amount;

    const btnWebsite = document.getElementById('feed-website-btn');
    const btnWhatsApp = document.getElementById('feed-whatsapp-btn');
    
    if (cow.payment_method === 'whatsapp') {
        btnWebsite.style.display = 'none';
        btnWhatsApp.style.display = 'block';
    } else if (cow.payment_method === 'both') {
        btnWebsite.style.display = 'block';
        btnWhatsApp.style.display = 'block';
    } else {
        btnWebsite.style.display = 'block';
        btnWhatsApp.style.display = 'none';
    }
}

function setQuickAmount(addVal) {
    const input = document.getElementById('custom-amount-input');
    const currentVal = parseFloat(input.value) || 0;
    input.value = currentVal + addVal;
}

function resetSuggestedAmount() {
    const cowId = document.querySelector('select[name="cow_id"]').value;
    const cow = cowsData[cowId];
    if (cow) {
        document.getElementById('custom-amount-input').value = cow.feed_amount;
    } else {
        document.getElementById('custom-amount-input').value = 500;
    }
}

function handleFeedingSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const cowId = form.cow_id.value;
    const amount = form.amount.value;
    const name = form.name.value;
    const email = form.email.value;
    const phone = form.phone.value;
    const pan = form.pan.value;

    if (!cowId) {
        showToast('Please select a cow to feed.', 'warning');
        return;
    }

    const cow = cowsData[cowId];
    const cowName = cow ? cow.name : 'Sacred Cow';

    window.location.href = `/Kamadhenu-goushala/checkout.php?type=feed_cow&cow_id=${cowId}&amount=${amount}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&pan=${encodeURIComponent(pan)}&description=${encodeURIComponent('Feed Contribution for ' + cowName)}`;
}

function submitFeedingWhatsApp() {
    const form = document.getElementById('feedCowForm');
    const cowId = form.cow_id.value;
    const amount = parseFloat(form.amount.value) || 0;
    const donorName = form.name.value.trim();
    const donorEmail = form.email.value.trim();
    const donorPhone = form.phone.value.trim();
    const donorPan = form.pan.value.trim();

    if (!cowId) {
        showToast('Please select a cow first.', 'warning');
        return;
    }
    if (!donorName || !donorEmail || !donorPhone) {
        showToast('Please fill in your name, email, and phone number.', 'warning');
        return;
    }

    const cow = cowsData[cowId];
    const btnWebsite = document.getElementById('feed-website-btn');
    const btnWhatsApp = document.getElementById('feed-whatsapp-btn');
    const originalWaText = btnWhatsApp.innerHTML;

    btnWebsite.disabled = true;
    btnWhatsApp.disabled = true;
    btnWhatsApp.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Connecting WhatsApp...';

    fetch('/Kamadhenu-goushala/api/payments.php?action=verify', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            payment_method: 'whatsapp',
            entity_type: 'FeedCow',
            entity_id: cowId,
            amount: amount,
            name: donorName,
            email: donorEmail,
            phone: donorPhone,
            pan: donorPan,
            status: 'pending'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            let msg = '';
            if (cow.whatsapp_message && cow.whatsapp_message.trim() !== '') {
                msg = cow.whatsapp_message.trim() + `\n\n*(Reference: ${data.data.payment_id})*`;
            } else {
                msg = `Hare Krishna! I would like to feed a cow at Kamadenu Goushala Trust.\n\n`;
                msg += `*Feeding Details*:\n`;
                msg += `- Cow: ${cow.cow_code} - ${cow.name}\n`;
                msg += `- Contribution Amount: ₹${amount.toLocaleString('en-IN')}\n\n`;
                msg += `*Donor Details*:\n`;
                msg += `- Name: ${donorName}\n`;
                msg += `- Email: ${donorEmail}\n`;
                msg += `- Phone: ${donorPhone}\n`;
                if (donorPan) {
                    msg += `- PAN Card: ${donorPan}\n`;
                }
                msg += `\n_Please confirm my contribution. (Reference: ${data.data.payment_id})_`;
            }

            const cleanPhone = cow.whatsapp_phone.replace(/[^0-9]/g, '');
            const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
            window.location.href = waUrl;
        } else {
            showToast('Error generating feeding request: ' + data.message, 'danger');
            btnWebsite.disabled = false;
            btnWhatsApp.disabled = false;
            btnWhatsApp.innerHTML = originalWaText;
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Server verification request failed.', 'danger');
        btnWebsite.disabled = false;
        btnWhatsApp.disabled = false;
        btnWhatsApp.innerHTML = originalWaText;
    });
}

// Pre-fill selection if cow_id passed in URL query
document.addEventListener("DOMContentLoaded", function() {
    const initialCowId = "<?php echo $selected_cow_id; ?>";
    if (initialCowId && cowsData[initialCowId]) {
        document.querySelector('select[name="cow_id"]').value = initialCowId;
        onCowSelectChanged(initialCowId);
    } else {
        onCowSelectChanged("");
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
