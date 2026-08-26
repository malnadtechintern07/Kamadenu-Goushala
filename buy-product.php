<?php
require_once __DIR__ . '/includes/header.php';
require_user_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, wn.phone_number as wa_phone_dir FROM products p LEFT JOIN product_categories c ON p.category_id = c.id LEFT JOIN whatsapp_numbers wn ON p.whatsapp_number_id = wn.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<section class="py-5 text-center"><div class="container py-5"><div class="alert alert-warning d-inline-block px-5 py-4"><h4 class="font-heading mb-2">Product Not Found</h4><p class="mb-3 text-muted font-ui">The selected product is unavailable or invalid.</p><a href="/Kamadenu/products.php" class="btn btn-warning font-ui fw-bold">Return to Product Store</a></div></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$product_checkout_method = get_setting($pdo, 'product_checkout_method', 'both');

$wa_phone = !empty($product['wa_phone_dir']) ? $product['wa_phone_dir'] : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
$wa_msg = !empty($product['whatsapp_message']) ? $product['whatsapp_message'] : "Hare Krishna! I would like to purchase this product:\n- Product: " . $product['name'] . "\n- Price: ₹" . number_format($product['price'], 2) . "\n\nPlease let me know how to proceed.";
$whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="font-heading text-warning mb-1"><i class="fas fa-shopping-bag text-warning me-2"></i> Product Checkout Options</h2>
                <p class="text-white-50 small mb-0">Select your preferred checkout method to complete your purchase of A2 Goushala products.</p>
            </div>
            <a href="/Kamadenu/products.php" class="btn btn-outline-warning btn-sm font-ui"><i class="fas fa-arrow-left me-1"></i> Back to Products</a>
        </div>
    </div>
</section>

<section class="py-5 bg-card">
    <div class="container py-3">
        <div class="row justify-content-center g-4">
            
            <!-- Selected Product Summary Card -->
            <div class="col-lg-5">
                <div class="kamadenu-card p-4 shadow-lg border-warning h-100">
                    <span class="badge bg-warning text-dark font-ui fw-bold mb-3 px-3 py-2">
                        <i class="fas fa-certificate me-1"></i> <?php echo e($product['category_name'] ?: 'Goushala Product'); ?>
                    </span>

                    <img src="<?php echo img_url($product['image']); ?>" alt="<?php echo e($product['name']); ?>" class="img-fluid rounded-4 shadow mb-4 border border-warning w-100" style="height: 240px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">

                    <h3 class="font-heading mb-2 text-dark"><?php echo e($product['name']); ?></h3>
                    
                    <div class="fs-2 fw-bold text-success font-mono mb-3">₹<?php echo number_format($product['price'], 2); ?></div>

                    <div class="bg-light p-3 rounded-4 mb-3 font-ui small border">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Stock Status:</span>
                            <strong class="text-success"><?php echo $product['stock_quantity'] > 0 ? "In Stock ({$product['stock_quantity']} {$product['unit']})" : 'Out of Stock'; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Unit Size:</span>
                            <strong><?php echo e($product['unit']); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Quality Guarantee:</span>
                            <strong class="text-warning-dark">100% Pure A2 Organic</strong>
                        </div>
                    </div>

                    <p class="small text-secondary mb-0"><?php echo e(mb_strimwidth($product['description'], 0, 160, '...')); ?></p>
                </div>
            </div>

            <!-- Checkout Method Selection Cards -->
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5 shadow-lg h-100">
                    <h4 class="font-heading mb-2 text-warning"><i class="fas fa-shield-alt text-warning me-2"></i> Choose Checkout Method</h4>
                    <p class="text-muted small font-ui mb-4">Please select your preferred checkout method as configured by sanctuary management:</p>

                    <div class="d-grid gap-4">

                        <?php if ($product_checkout_method === 'website'): ?>
                            <!-- Website Checkout Card Only -->
                            <div class="card p-4 border-warning bg-light hover-glow rounded-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-warning text-dark p-3 rounded-circle fs-3 shadow-sm">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-heading mb-1 text-dark">Website Instant Checkout</h5>
                                        <small class="text-muted d-block font-ui">Pay securely online using UPI, Credit/Debit Cards, NetBanking or Razorpay.</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning btn-lg w-100 font-ui fw-bold py-3 shadow" onclick="proceedWebsiteCheckout(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')">
                                    <i class="fas fa-bolt me-2"></i> Proceed with Website Checkout &rarr;
                                </button>
                            </div>
                        <?php elseif ($product_checkout_method === 'whatsapp'): ?>
                            <!-- WhatsApp Checkout Card Only -->
                            <div class="card p-4 border-success bg-light hover-glow rounded-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success text-white p-3 rounded-circle fs-3 shadow-sm">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-heading mb-1 text-dark">WhatsApp Order &amp; Direct Inquiry</h5>
                                        <small class="text-muted d-block font-ui">Connect directly with our sanctuary team on WhatsApp to confirm delivery.</small>
                                    </div>
                                </div>
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success btn-lg w-100 font-ui fw-bold py-3 shadow text-center">
                                    <i class="fab fa-whatsapp me-2"></i> Order via WhatsApp &rarr;
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Both Options Available -->
                            <div class="card p-4 border-warning bg-light hover-glow rounded-4 mb-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-warning text-dark p-3 rounded-circle fs-3 shadow-sm">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-heading mb-1 text-dark">Website Instant Checkout</h5>
                                        <small class="text-muted d-block font-ui">Pay securely online using UPI, Credit/Debit Cards, NetBanking or Razorpay.</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning btn-lg w-100 font-ui fw-bold py-3 shadow" onclick="proceedWebsiteCheckout(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')">
                                    <i class="fas fa-bolt me-2"></i> Proceed with Website Checkout &rarr;
                                </button>
                            </div>

                            <div class="card p-4 border-success bg-light hover-glow rounded-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success text-white p-3 rounded-circle fs-3 shadow-sm">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-heading mb-1 text-dark">WhatsApp Order &amp; Direct Inquiry</h5>
                                        <small class="text-muted d-block font-ui">Connect directly with our sanctuary team on WhatsApp to confirm delivery.</small>
                                    </div>
                                </div>
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success btn-lg w-100 font-ui fw-bold py-3 shadow text-center">
                                    <i class="fab fa-whatsapp me-2"></i> Order via WhatsApp &rarr;
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
