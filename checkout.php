<?php
require_once __DIR__ . '/includes/header.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'donation'; // donation, sponsorship, seva, cart

$whatsapp_order_default = get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
$product_wa_map = $pdo->query("SELECT p.id, wn.phone_number FROM products p JOIN whatsapp_numbers wn ON p.whatsapp_number_id = wn.id WHERE p.whatsapp_number_id IS NOT NULL")->fetchAll(PDO::FETCH_KEY_PAIR);

$amount = 0;
$description = 'Gouseva Contribution';
$entity_id = 0;

$sponsorship_wa_msg = '';
$sponsorship_wa_phone = '';
$sponsorship_contact_method = 'website';

$seva_wa_msg = '';
$seva_wa_phone = '';
$seva_contact_method = 'website';

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
    let isWhatsApp = false;
    
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
    }

    let method = isWhatsApp ? 'WhatsApp' : 'UPI';
    if (!isWhatsApp) {
        if (document.getElementById('optCard').classList.contains('show')) {
            method = 'Card';
        } else if (document.getElementById('optBank').classList.contains('show')) {
            method = 'Bank Transfer';
        }
    }

    const payload = {
        action: 'verify_payment',
        payment_method: method,
        razorpay_payment_id: 'pay_' + Math.random().toString(36).substring(2, 12),
        razorpay_order_id: 'order_' + Math.random().toString(36).substring(2, 12),
        razorpay_signature: 'simulated_sig_' + Date.now(),
        entity_type: formData.get('type') === 'sponsorship' ? 'Sponsorship' : (formData.get('type') === 'seva' ? 'Seva' : (formData.get('type') === 'cart' ? 'Order' : 'Donation')),
        entity_id: formData.get('entity_id'),
        seva_id: formData.get('type') === 'seva' ? formData.get('entity_id') : null,
        cow_id: formData.get('type') === 'sponsorship' ? formData.get('entity_id') : null,
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

                if (formData.get('type') === 'cart') {
                    localStorage.removeItem('kamadenu_cart');
                    if (payload.items.length === 1) {
                        const singleItem = payload.items[0];
                        if (productWhatsAppMap[singleItem.id]) {
                            targetWhatsAppNum = productWhatsAppMap[singleItem.id];
                        }
                    }
                    const orderCode = (data.data && data.data.receipt_number) ? data.data.receipt_number : (data.receipt_number || payload.razorpay_order_id);

                    msg = `Hare Krishna! I would like to place an order at Kamadenu Goushala Store.\n\n`;
                    msg += `*Order Code*: ${orderCode}\n\n`;
                    msg += `*Items Ordered*:\n`;
                    payload.items.forEach(item => {
                        msg += `- ${item.name} x ${item.quantity} (₹${item.price.toLocaleString('en-IN')} each)\n`;
                    });
                    msg += `\n*Total Price*: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n`;
                    msg += `*Delivery Details*:\n`;
                    msg += `- Name: ${payload.customer_name}\n`;
                    msg += `- Phone: ${payload.customer_phone}\n`;
                    msg += `- Email: ${payload.customer_email}\n`;
                    msg += `- Shipping Address: ${payload.shipping_address}\n\n`;
                    msg += `_Please confirm my order. (Reference: ${refPaymentId})_`;

                } else if (formData.get('type') === 'donation') {
                    targetWhatsAppNum = donationWaPhone;
                    if (donationWaMsg && donationWaMsg.trim() !== '') {
                        msg = donationWaMsg.trim() + `\n\n*(Reference: ${refPaymentId})*`;
                    } else {
                        msg = `Hare Krishna! I have made a donation to Kamadenu Goushala.\n\n`;
                        msg += `*Donation Details*:\n`;
                        msg += `- Purpose: ${payload.purpose}\n`;
                        msg += `- Amount: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n`;
                        msg += `*Donor Details*:\n`;
                        msg += `- Name: ${payload.donor_name}\n`;
                        msg += `- Email: ${payload.donor_email}\n`;
                        msg += `- Phone: ${payload.donor_phone}\n\n`;
                        msg += `_Reference: ${refPaymentId}_`;
                    }

                } else if (formData.get('type') === 'sponsorship') {
                    targetWhatsAppNum = sponsorshipWaPhone;
                    if (sponsorshipWaMsg && sponsorshipWaMsg.trim() !== '') {
                        msg = sponsorshipWaMsg.trim() + `\n\n*(Reference: ${refPaymentId})*`;
                    } else {
                        msg = `Hare Krishna! I would like to sponsor a cow at Kamadenu Goushala.\n\n`;
                        msg += `*Sponsorship Details*:\n`;
                        msg += `- Details: ${payload.purpose}\n`;
                        msg += `- Amount: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n`;
                        msg += `*Sponsor Details*:\n`;
                        msg += `- Name: ${payload.donor_name}\n`;
                        msg += `- Email: ${payload.donor_email}\n`;
                        msg += `- Phone: ${payload.donor_phone}\n\n`;
                        msg += `_Reference: ${refPaymentId}_`;
                    }

                } else if (formData.get('type') === 'seva') {
                    targetWhatsAppNum = sevaWaPhone;
                    if (sevaWaMsg && sevaWaMsg.trim() !== '') {
                        msg = sevaWaMsg.trim() + `\n\n*(Reference: ${refPaymentId})*`;
                    } else {
                        msg = `Hare Krishna! I would like to sponsor a seva at Kamadenu Goushala.\n\n`;
                        msg += `*Seva Details*:\n`;
                        msg += `- Seva: ${payload.purpose}\n`;
                        msg += `- Suggested Amount: ₹${parseFloat(payload.amount).toLocaleString('en-IN')}\n\n`;
                        msg += `*Sponsor Details*:\n`;
                        msg += `- Name: ${payload.donor_name}\n`;
                        msg += `- Email: ${payload.donor_email}\n`;
                        msg += `- Phone: ${payload.donor_phone}\n\n`;
                        msg += `_Reference: ${refPaymentId}_`;
                    }
                }

                const cleanPhone = targetWhatsAppNum.replace(/[^0-9]/g, '');
                const waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`;
                window.location.href = waUrl;
            } else {
                if (isCart) {
                    localStorage.removeItem('kamadenu_cart');
                }
                const pId = (data.data && data.data.payment_id) ? data.data.payment_id : (data.payment_id || '');
                const rcpt = (data.data && data.data.receipt_number) ? data.data.receipt_number : (data.receipt_number || '');
                window.location.href = '/Kamadenu/thank-you.php?payment_id=' + pId + '&receipt=' + rcpt + '&amount=' + formData.get('amount');
            }
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
