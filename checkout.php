<?php
require_once __DIR__ . '/includes/header.php';

if (!is_user_logged_in()) {
    $redirect_url = $_SERVER['REQUEST_URI'];
    header("Location: /Kamadenu/login.php?redirect=" . urlencode($redirect_url) . "&msg=login_required");
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'donation'; // donation, sponsorship, seva, cart

$whatsapp_order_default = get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
$product_wa_map = $pdo->query("SELECT p.id, wn.phone_number FROM products p JOIN whatsapp_numbers wn ON p.whatsapp_number_id = wn.id WHERE p.whatsapp_number_id IS NOT NULL")->fetchAll(PDO::FETCH_KEY_PAIR);
$product_method_map = $pdo->query("SELECT id, contact_method FROM products")->fetchAll(PDO::FETCH_KEY_PAIR);

$amount = 0;
$description = 'Gouseva Contribution';
$entity_id = 0;

$sponsorship_wa_msg = '';
$sponsorship_wa_phone = '';
$sponsorship_contact_method = 'website';

$seva_wa_msg = '';
$seva_wa_phone = '';
$seva_contact_method = 'website';

$feed_wa_msg = '';
$feed_wa_phone = '';
$feed_contact_method = 'website';

$feed_cow_wa_msg = '';
$feed_cow_wa_phone = '';
$feed_cow_contact_method = 'website';

$campaign_id = isset($_GET['campaign_id']) ? intval($_GET['campaign_id']) : 0;
$campaign_wa_msg = '';
$campaign_wa_phone = '';
$campaign_contact_method = 'website';

if ($type === 'donation') {
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 2500.00;
    $description = isset($_GET['purpose']) ? $_GET['purpose'] : 'General Gouseva Donation';
    
    if ($campaign_id > 0) {
        $stmt = $pdo->prepare("SELECT ec.*, wn.phone_number as wa_phone_dir FROM emergency_campaigns ec LEFT JOIN whatsapp_numbers wn ON ec.whatsapp_number_id = wn.id WHERE ec.id = ?");
        $stmt->execute([$campaign_id]);
        $camp = $stmt->fetch();
        if ($camp) {
            $campaign_contact_method = $camp['contact_method'];
            $campaign_wa_phone = !empty($camp['wa_phone_dir']) ? $camp['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
            $campaign_wa_msg = !empty($camp['whatsapp_message']) ? $camp['whatsapp_message'] : '';
            $description = "Emergency Campaign: " . $camp['title'];
            $entity_id = $campaign_id;
        }
    }
} elseif ($type === 'sponsorship') {
    $cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 1;
    $dur = isset($_GET['duration']) ? intval($_GET['duration']) : 1;
    
    $stmt = $pdo->prepare("SELECT c.*, wn.phone_number as wa_phone_dir FROM cows c LEFT JOIN whatsapp_numbers wn ON c.whatsapp_number_id = wn.id WHERE c.id = ?");
    $stmt->execute([$cow_id]);
    $cow = $stmt->fetch();
    
    $monthly = $cow ? floatval($cow['monthly_sponsorship_amount']) : 2500.00;
    $amount = $monthly * $dur;
    $description = "Sponsorship of cow " . ($cow ? $cow['name'] : 'Indigenous Cow') . " (" . ($cow ? $cow['cow_code'] : '') . ") for {$dur} Months";
    $entity_id = $cow_id;

    if ($cow) {
        $sponsorship_contact_method = $cow['contact_method'];
        $sponsorship_wa_phone = !empty($cow['wa_phone_dir']) ? $cow['wa_phone_dir'] : get_setting($pdo, 'whatsapp_adoption_default', '+91 98800 12345');
        $sponsorship_wa_msg = !empty($cow['whatsapp_message']) ? $cow['whatsapp_message'] : '';
    }
} elseif ($type === 'seva') {
    $seva_id = isset($_GET['seva_id']) ? intval($_GET['seva_id']) : 1;
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 1000.00;
    
    $stmt = $pdo->prepare("SELECT s.*, wn.phone_number as wa_phone_dir FROM seva s LEFT JOIN whatsapp_numbers wn ON s.whatsapp_number_id = wn.id WHERE s.id = ?");
    $stmt->execute([$seva_id]);
    $seva = $stmt->fetch();
    $description = "Seva Sponsorship: " . ($seva ? $seva['title'] : 'Gouseva');
    $entity_id = $seva_id;

    if ($seva) {
        $seva_contact_method = $seva['contact_method'];
        $seva_wa_phone = !empty($seva['wa_phone_dir']) ? $seva['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
        $seva_wa_msg = !empty($seva['whatsapp_message']) ? $seva['whatsapp_message'] : '';
    }
} elseif ($type === 'feed') {
    $feed_id = isset($_GET['feed_id']) ? intval($_GET['feed_id']) : 1;
    $qty = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
    
    $stmt = $pdo->prepare("SELECT f.*, wn.phone_number as wa_phone_dir FROM feed_items f LEFT JOIN whatsapp_numbers wn ON f.whatsapp_number_id = wn.id WHERE f.id = ?");
    $stmt->execute([$feed_id]);
    $feed = $stmt->fetch();
    
    $cost = $feed ? floatval($feed['cost']) : 500.00;
    $amount = $cost * $qty;
    $description = "Feed Cow: " . ($feed ? $feed['title'] : 'Fodder') . " x{$qty}";
    $entity_id = $feed_id;

    if ($feed) {
        $feed_contact_method = $feed['contact_method'];
        $feed_wa_phone = !empty($feed['wa_phone_dir']) ? $feed['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
        $feed_wa_msg = !empty($feed['whatsapp_message']) ? $feed['whatsapp_message'] : '';
    }
} elseif ($type === 'feed_cow') {
    $cow_id = isset($_GET['cow_id']) ? intval($_GET['cow_id']) : 0;
    
    $stmt = $pdo->prepare("SELECT fc.*, wn.phone_number as wa_phone_dir FROM feeding_cows fc LEFT JOIN whatsapp_numbers wn ON fc.whatsapp_number_id = wn.id WHERE fc.id = ?");
    $stmt->execute([$cow_id]);
    $cow = $stmt->fetch();
    
    $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : ($cow ? floatval($cow['feed_amount']) : 500.00);
    $description = "Feeding contribution for cow " . ($cow ? $cow['name'] : 'Indigenous Cow') . " (" . ($cow ? $cow['cow_code'] : '') . ")";
    $entity_id = $cow_id;

    if ($cow) {
        $feed_cow_contact_method = $cow['payment_method'];
        $feed_cow_wa_phone = !empty($cow['wa_phone_dir']) ? $cow['wa_phone_dir'] : get_setting($pdo, 'whatsapp_adoption_default', '+91 98800 12345');
        $feed_cow_wa_msg = !empty($cow['whatsapp_message']) ? $cow['whatsapp_message'] : '';
    }
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
                        <?php if ($type === 'cart'): ?>
                            <h4 class="font-heading fs-5 mb-3"><i class="fas fa-truck me-2 text-primary"></i> Shipping Details</h4>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label font-ui small fw-bold">Full Name</label>
                                    <input type="text" name="donor_name" class="form-control" value="<?php echo e($donor_name !== 'Devotee' ? $donor_name : ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-ui small fw-bold">Phone Number</label>
                                    <input type="text" name="donor_phone" class="form-control" value="<?php echo e($donor_phone !== '+91 99000 00000' ? $donor_phone : ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label font-ui small fw-bold">Email Address</label>
                                    <input type="email" name="donor_email" class="form-control" value="<?php echo e($donor_email !== 'devotee@kamadenugoushala.org' ? $donor_email : ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label font-ui small fw-bold">Complete Shipping Address (with PIN Code)</label>
                                    <textarea name="shipping_address" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="donor_name" value="<?php echo e($donor_name); ?>">
                            <input type="hidden" name="donor_email" value="<?php echo e($donor_email); ?>">
                            <input type="hidden" name="donor_phone" value="<?php echo e($donor_phone); ?>">
                            <input type="hidden" name="shipping_address" value="">
                        <?php endif; ?>

                        <!-- WhatsApp Checkout Notice -->
                        <div id="checkoutNoticeWhatsApp" class="alert alert-info border-info align-items-center gap-3 mb-4" style="display: none;">
                            <i class="fab fa-whatsapp fs-2 text-success"></i>
                            <div>
                                <strong class="d-block font-heading text-dark">WhatsApp Order Placement</strong>
                                <span class="small text-muted">Your order details and shipping address will be sent to the Goushala administrator on WhatsApp for confirmation.</span>
                            </div>
                        </div>

                        <!-- Standard Payment Accordion -->
                        <div id="paymentOptionsAccordionContainer" class="accordion mb-4" style="display: none;">
                            <!-- Option 1: UPI Payment (Google Pay / PhonePe / Paytm) -->
                            <div id="paymentItemUPI" class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optUPI" checked>
                                        <i class="fas fa-mobile-alt me-2 text-success"></i> Instant UPI / QR Code (Google Pay, PhonePe, Paytm, BHIM)
                                    </button>
                                </h2>
                                <div id="optUPI" class="accordion-collapse collapse show" data-bs-parent="#paymentOptionsAccordionContainer">
                                    <div class="accordion-body text-start">
                                        <div class="text-center p-3 bg-light rounded mb-3 text-dark">
                                            <?php 
                                            $qr_code_setting = get_setting($pdo, 'donation_qr_code', 'assets/images/donation_qr.png');
                                            $upi_id_setting = get_setting($pdo, 'donation_upi_id', 'kamadenu@upi');
                                            ?>
                                            <img src="<?php echo htmlspecialchars(img_url($qr_code_setting)); ?>" alt="Donation QR Code" class="img-fluid mb-2 rounded-3 shadow-sm border border-secondary" style="max-height: 180px; width: auto;">
                                            <div class="font-mono fw-bold">UPI ID: <?php echo e($upi_id_setting); ?></div>
                                            <small class="text-muted">Scan QR code or transfer to official Goushala UPI ID</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label font-ui small fw-bold">Enter Your UPI ID (e.g. user@okaxis) or UTR Number</label>
                                            <input type="text" name="upi_id" class="form-control" placeholder="yourname@upi or 12-digit UTR">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Razorpay Sandbox Cards & NetBanking -->
                            <div id="paymentItemCard" class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optCard">
                                        <i class="fas fa-credit-card me-2 text-warning"></i> Credit / Debit Cards & NetBanking (Razorpay)
                                    </button>
                                </h2>
                                <div id="optCard" class="accordion-collapse collapse" data-bs-parent="#paymentOptionsAccordionContainer">
                                    <div class="accordion-body text-start">
                                        <p class="small text-muted">Supports Visa, MasterCard, RuPay, SBI, HDFC, ICICI NetBanking.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 3: Direct Bank Transfer (NEFT / RTGS) -->
                            <div id="paymentItemBank" class="accordion-item kamadenu-card mb-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optBank">
                                        <i class="fas fa-university me-2 text-primary"></i> Direct Bank Transfer (NEFT / RTGS / IMPS)
                                    </button>
                                </h2>
                                <div id="optBank" class="accordion-collapse collapse" data-bs-parent="#paymentOptionsAccordionContainer">
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

                            <!-- Option 4: WhatsApp Checkout (Confirm via WhatsApp) -->
                            <div id="paymentItemWhatsApp" class="accordion-item kamadenu-card mb-3 border" style="display: none;">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed font-heading fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#optWhatsApp">
                                        <i class="fab fa-whatsapp me-2 text-success"></i> Confirm &amp; Complete via WhatsApp
                                    </button>
                                </h2>
                                <div id="optWhatsApp" class="accordion-collapse collapse" data-bs-parent="#paymentOptionsAccordionContainer">
                                    <div class="accordion-body text-start">
                                        <p class="small text-muted">Select this option to send your order details, shipping info, or donation preferences directly to the Goushala administrator on WhatsApp for instant confirmation.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="checkoutSubmitBtn" class="btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow">
                            <i class="fas fa-check-circle me-2"></i> Complete Payment & Update Admin Panel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const type = <?php echo json_encode($type); ?>;
const defaultWhatsAppOrder = <?php echo json_encode($whatsapp_order_default); ?>;
const productWhatsAppMap = <?php echo json_encode($product_wa_map); ?>;
const productMethodMap = <?php echo json_encode($product_method_map); ?>;
const productCheckoutMethod = <?php echo json_encode(get_setting($pdo, 'product_checkout_method', 'both')); ?>;

const donationActionMode = <?php echo json_encode(get_setting($pdo, 'donation_action_mode', 'website')); ?>;
const donationWaPhone = <?php echo json_encode(get_setting($pdo, 'whatsapp_donation_default', '+91 98800 12345')); ?>;
const donationWaMsg = <?php echo json_encode(get_setting($pdo, 'whatsapp_donation_message', '')); ?>;

const campaignContactMethod = <?php echo json_encode($campaign_contact_method); ?>;
const campaignWaPhone = <?php echo json_encode($campaign_wa_phone); ?>;
const campaignWaMsg = <?php echo json_encode($campaign_wa_msg); ?>;

const sponsorshipContactMethod = <?php echo json_encode($sponsorship_contact_method); ?>;
const sponsorshipWaPhone = <?php echo json_encode($sponsorship_wa_phone); ?>;
const sponsorshipWaMsg = <?php echo json_encode($sponsorship_wa_msg); ?>;
const durationMonths = <?php echo isset($dur) ? intval($dur) : 1; ?>;

const sevaContactMethod = <?php echo json_encode($seva_contact_method); ?>;
const sevaWaPhone = <?php echo json_encode($seva_wa_phone); ?>;
const sevaWaMsg = <?php echo json_encode($seva_wa_msg); ?>;

const feedContactMethod = <?php echo json_encode($feed_contact_method); ?>;
const feedWaPhone = <?php echo json_encode($feed_wa_phone); ?>;
const feedWaMsg = <?php echo json_encode($feed_wa_msg); ?>;

const feedCowContactMethod = <?php echo json_encode($feed_cow_contact_method); ?>;
const feedCowWaPhone = <?php echo json_encode($feed_cow_wa_phone); ?>;
const feedCowWaMsg = <?php echo json_encode($feed_cow_wa_msg); ?>;

let isWhatsApp = false;

function selectAccordionItem(targetId) {
    const items = ['optUPI', 'optCard', 'optBank', 'optWhatsApp'];
    items.forEach(id => {
        const bodyEl = document.getElementById(id);
        const btnEl = document.querySelector(`button[data-bs-target="#${id}"]`);
        if (id === targetId) {
            if (bodyEl) bodyEl.classList.add('show');
            if (btnEl) {
                btnEl.classList.remove('collapsed');
                btnEl.setAttribute('aria-expanded', 'true');
            }
        } else {
            if (bodyEl) bodyEl.classList.remove('show');
            if (btnEl) {
                btnEl.classList.add('collapsed');
                btnEl.setAttribute('aria-expanded', 'false');
            }
        }
    });
}

// Perform dynamic switches on page load
document.addEventListener("DOMContentLoaded", function() {
    let resolvedMethod = 'website';

    if (type === 'cart') {
        resolvedMethod = productCheckoutMethod;
    } else if (type === 'donation') {
        resolvedMethod = campaignContactMethod !== 'website' ? campaignContactMethod : donationActionMode;
    } else if (type === 'sponsorship') {
        resolvedMethod = sponsorshipContactMethod;
    } else if (type === 'seva') {
        resolvedMethod = sevaContactMethod;
    } else if (type === 'feed') {
        resolvedMethod = feedContactMethod;
    } else if (type === 'feed_cow') {
        resolvedMethod = feedCowContactMethod;
    }

    const noticeEl = document.getElementById('checkoutNoticeWhatsApp');
    const accordionEl = document.getElementById('paymentOptionsAccordionContainer');
    const btn = document.getElementById('checkoutSubmitBtn');

    const itemUPI = document.getElementById('paymentItemUPI');
    const itemCard = document.getElementById('paymentItemCard');
    const itemBank = document.getElementById('paymentItemBank');
    const itemWhatsApp = document.getElementById('paymentItemWhatsApp');

    function updateSubmitBtn(isWa) {
        if (!btn) return;
        if (isWa) {
            if (type === 'seva') {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Sponsor Seva via WhatsApp';
            } else if (type === 'sponsorship') {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Sponsor Cow via WhatsApp';
            } else if (type === 'donation') {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Donate via WhatsApp';
            } else if (type === 'feed') {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Feed Cow via WhatsApp';
            } else if (type === 'feed_cow') {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Feed Cow via WhatsApp';
            } else {
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Place Order via WhatsApp';
            }
            btn.className = 'btn btn-success btn-lg w-100 py-3 font-ui fw-bold shadow';
        } else {
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Complete Payment & Update Admin Panel';
            btn.className = 'btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow';
        }
    }

    // Always show the container
    if (accordionEl) accordionEl.style.setProperty('display', 'block', 'important');

    if (resolvedMethod === 'whatsapp') {
        isWhatsApp = true;
        
        // Show WhatsApp option and hide website ones
        if (itemUPI) itemUPI.style.setProperty('display', 'none', 'important');
        if (itemCard) itemCard.style.setProperty('display', 'none', 'important');
        if (itemBank) itemBank.style.setProperty('display', 'none', 'important');
        if (itemWhatsApp) itemWhatsApp.style.setProperty('display', 'block', 'important');

        selectAccordionItem('optWhatsApp');
        if (noticeEl) noticeEl.style.setProperty('display', 'flex', 'important');
        updateSubmitBtn(true);

    } else if (resolvedMethod === 'both') {
        isWhatsApp = false; // default to website accordion choice
        
        // Show all options
        if (itemUPI) itemUPI.style.setProperty('display', 'block', 'important');
        if (itemCard) itemCard.style.setProperty('display', 'block', 'important');
        if (itemBank) itemBank.style.setProperty('display', 'block', 'important');
        if (itemWhatsApp) itemWhatsApp.style.setProperty('display', 'block', 'important');

        selectAccordionItem('optUPI');
        if (noticeEl) noticeEl.style.setProperty('display', 'none', 'important');
        updateSubmitBtn(false);

        // Hook up collapse listeners to detect choice toggles
        const optWhatsAppCollapse = document.getElementById('optWhatsApp');
        if (optWhatsAppCollapse) {
            optWhatsAppCollapse.addEventListener('show.bs.collapse', function() {
                isWhatsApp = true;
                if (noticeEl) noticeEl.style.setProperty('display', 'flex', 'important');
                updateSubmitBtn(true);
            });
            optWhatsAppCollapse.addEventListener('hide.bs.collapse', function() {
                isWhatsApp = false;
                if (noticeEl) noticeEl.style.setProperty('display', 'none', 'important');
                updateSubmitBtn(false);
            });
        }
    } else {
        isWhatsApp = false;

        // Show website options and hide WhatsApp
        if (itemUPI) itemUPI.style.setProperty('display', 'block', 'important');
        if (itemCard) itemCard.style.setProperty('display', 'block', 'important');
        if (itemBank) itemBank.style.setProperty('display', 'block', 'important');
        if (itemWhatsApp) itemWhatsApp.style.setProperty('display', 'none', 'important');

        selectAccordionItem('optUPI');
        if (noticeEl) noticeEl.style.setProperty('display', 'none', 'important');
        updateSubmitBtn(false);
    }
});

function handlePaymentSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);

    const isCart = formData.get('type') === 'cart';
    const optWhatsAppCollapse = document.getElementById('optWhatsApp');
    let isWhatsApp = optWhatsAppCollapse ? optWhatsAppCollapse.classList.contains('show') : false;
    
    if (!isWhatsApp) {
        if (isCart) {
            const cart = JSON.parse(localStorage.getItem('kamadenu_cart') || '[]');
            cart.forEach(item => {
                if (productMethodMap[item.id] === 'whatsapp') {
                    isWhatsApp = true;
                }
            });
        } else if (formData.get('type') === 'donation') {
            if (donationActionMode === 'whatsapp') {
                isWhatsApp = true;
            }
        } else if (formData.get('type') === 'sponsorship') {
            if (sponsorshipContactMethod === 'whatsapp') {
                isWhatsApp = true;
            }
        } else if (formData.get('type') === 'seva') {
            if (sevaContactMethod === 'whatsapp') {
                isWhatsApp = true;
            }
        } else if (formData.get('type') === 'feed') {
            if (feedContactMethod === 'whatsapp') {
                isWhatsApp = true;
            }
        } else if (formData.get('type') === 'feed_cow') {
            if (feedCowContactMethod === 'whatsapp') {
                isWhatsApp = true;
            }
        }
    }

    let method = isWhatsApp ? 'WhatsApp' : 'UPI';
    if (!isWhatsApp) {
        if (document.getElementById('optCard').classList.contains('show')) {
            method = 'Card';
        } else if (document.getElementById('optBank').classList.contains('show')) {
            method = 'Bank Transfer';
        }
    }

    let mockPaymentId = 'pay_' + Math.random().toString(36).substring(2, 12);
    if (method === 'Bank Transfer' && formData.get('utr_number')) {
        mockPaymentId = 'UTR-' + formData.get('utr_number').trim();
    } else if (method === 'UPI' && formData.get('upi_id')) {
        mockPaymentId = 'UPI-' + formData.get('upi_id').trim();
    }

    let pendingPayload = {
        action: 'verify_payment',
        payment_method: method,
        payment_id: mockPaymentId,
        razorpay_payment_id: mockPaymentId,
        razorpay_order_id: 'order_' + Math.random().toString(36).substring(2, 12),
        razorpay_signature: 'simulated_sig_' + Date.now(),
        entity_type: formData.get('type') === 'sponsorship' ? 'Sponsorship' : (formData.get('type') === 'seva' ? 'Seva' : (formData.get('type') === 'feed' ? 'Feed' : (formData.get('type') === 'feed_cow' ? 'FeedCow' : (formData.get('type') === 'cart' ? 'Order' : 'Donation')))),
        entity_id: formData.get('entity_id'),
        seva_id: formData.get('type') === 'seva' ? formData.get('entity_id') : null,
        cow_id: (formData.get('type') === 'sponsorship' || formData.get('type') === 'feed_cow') ? formData.get('entity_id') : null,
        sponsor_name: formData.get('donor_name'),
        sponsor_email: formData.get('donor_email'),
        sponsor_phone: formData.get('donor_phone'),
        duration_months: typeof durationMonths !== 'undefined' ? durationMonths : 1,
        amount: formData.get('amount'),
        donor_name: formData.get('donor_name'),
        donor_email: formData.get('donor_email'),
        donor_phone: formData.get('donor_phone'),
        customer_name: formData.get('donor_name'),
        customer_email: formData.get('donor_email'),
        customer_phone: formData.get('donor_phone'),
        shipping_address: formData.get('shipping_address') || 'Provided during checkout',
        items: formData.get('type') === 'cart' ? JSON.parse(localStorage.getItem('kamadenu_cart') || '[]') : [],
        purpose: '<?php echo addslashes($description); ?>'
    };

    window.currentPendingPayload = pendingPayload;
    window.isWhatsAppCurrent = isWhatsApp;
    window.isCartCurrent = isCart;
    window.formDataCurrent = formData;

    if (isWhatsApp) {
        // Send WhatsApp Pending Approval Request
        executePaymentVerification(pendingPayload, true);
    } else {
        // Show Gateway Verification Modal for Website Payments
        document.getElementById('pg-amount-display').textContent = `₹ ${parseFloat(formData.get('amount')).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        document.getElementById('pg-purpose-display').textContent = pendingPayload.purpose;
        
        const modalEl = document.getElementById('paymentGatewayModal');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }
}

function confirmPaymentVerification() {
    if (!window.currentPendingPayload) return;
    window.currentPendingPayload.status = 'success';
    
    const modalEl = document.getElementById('paymentGatewayModal');
    const bsModal = bootstrap.Modal.getInstance(modalEl);
    if (bsModal) bsModal.hide();

    executePaymentVerification(window.currentPendingPayload, false);
}

function cancelPaymentVerification() {
    if (!window.currentPendingPayload) return;
    window.currentPendingPayload.status = 'failed';
    
    const modalEl = document.getElementById('paymentGatewayModal');
    const bsModal = bootstrap.Modal.getInstance(modalEl);
    if (bsModal) bsModal.hide();

    executePaymentVerification(window.currentPendingPayload, false);
}

function executePaymentVerification(payload, isWhatsApp) {
    showToast('Verifying payment status...', 'info');

    fetch('/Kamadenu/api/payments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            if (isWhatsApp) {
                let targetWhatsAppNum = defaultWhatsAppOrder;
                let msg = '';
                const refPaymentId = (data.data && data.data.payment_id) ? data.data.payment_id : (data.payment_id || 'pay_unknown');

                if (payload.entity_type === 'Order') {
                    if (window.isCartCurrent) localStorage.removeItem('kamadenu_cart');
                    msg = `Hare Krishna! I would like to place an order at Kamadenu Goushala Store.\n\n*Order Reference*: ${refPaymentId}\n*Total Price*: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n_Please verify & confirm my order._`;
                } else {
                    msg = `Hare Krishna! I have submitted a ${payload.entity_type} contribution to Kamadenu Goushala.\n\n*Details*: ${payload.purpose}\n*Amount*: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n_Reference ID: ${refPaymentId}_`;
                }

                const cleanPhone = targetWhatsAppNum.replace(/[^0-9]/g, '');
                const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
                window.location.href = waUrl;
            } else {
                if (window.isCartCurrent && payload.status === 'success') {
                    localStorage.removeItem('kamadenu_cart');
                }
                const pId = (data.data && data.data.payment_id) ? data.data.payment_id : (data.payment_id || '');
                const rcpt = (data.data && data.data.receipt_number) ? data.data.receipt_number : (data.receipt_number || '');
                const finalStatus = (payload.status === 'failed') ? 'failed' : 'completed';
                
                window.location.href = '/Kamadenu/thank-you.php?payment_id=' + pId + '&receipt=' + rcpt + '&amount=' + payload.amount + '&status=' + finalStatus;
            }
        } else {
            showToast('Payment processing alert: ' + data.message, 'danger');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Payment verification request failed.', 'danger');
    });
}
</script>

<!-- Payment Gateway Verification Modal -->
<div class="modal fade" id="paymentGatewayModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white border-warning">
                <h5 class="modal-title font-heading text-warning"><i class="fas fa-shield-alt me-2"></i> Kamadenu Payment Gateway</h5>
                <button type="button" class="btn-close btn-close-white" onclick="cancelPaymentVerification()"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-credit-card text-warning display-3"></i>
                </div>
                <h3 class="font-heading mb-1 text-dark" id="pg-amount-display">₹ 0.00</h3>
                <p class="text-muted small mb-4 font-ui" id="pg-purpose-display">Gouseva Contribution</p>
                
                <div class="alert alert-warning small font-ui text-start mb-4 border-warning">
                    <i class="fas fa-info-circle me-1 text-warning"></i> Complete secure payment verification or simulate transaction response:
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg font-ui fw-bold shadow py-3" onclick="confirmPaymentVerification()">
                        <i class="fas fa-check-circle me-2"></i> Pay &amp; Complete Verification
                    </button>
                    <button type="button" class="btn btn-outline-danger font-ui fw-semibold py-2" onclick="cancelPaymentVerification()">
                        <i class="fas fa-times-circle me-2"></i> Cancel Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
