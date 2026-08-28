<?php require_once __DIR__ . '/floating-controls.php'; ?>

<!-- Footer Section -->
<footer class="bg-dark text-light pt-5 pb-4 mt-5 border-top border-warning">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <img src="/Kamadhenu-goushala/assets/images/logo.png" alt="Kamadenu Goushala Trust Logo" class="me-3 rounded-3 shadow-sm" style="height: 64px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));">

                    <div>
                        <h4 class="font-heading text-warning mb-0"><?php echo __t('site_title'); ?></h4>
                        <small class="text-warning-50 font-ui fs-7">SACRED GOUSHALA TRUST</small>
                    </div>
                </div>
                <p class="text-secondary-50 small"><?php echo __t('footer_about'); ?></p>
                <div class="devotional-phrase text-warning fs-5 my-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                <p class="small text-muted mb-0"><i class="fas fa-ribbon text-warning me-1"></i> <?php echo __t('footer_gov_reg'); ?></p>
            </div>


            <div class="col-lg-2 col-md-6">
                <h5 class="font-ui text-white fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/Kamadhenu-goushala/cows.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-1 text-warning small"></i> Indigenous Cows</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/adopt.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-1 text-warning small"></i> Adopt a Cow</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/seva.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-1 text-warning small"></i> Daily Seva</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/donate.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-1 text-warning small"></i> Donate Now</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/products.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-1 text-warning small"></i> Goushala Store</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="font-ui text-white fw-bold mb-3">Statutory & Policies</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/Kamadhenu-goushala/privacy.php" class="text-light text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/terms.php" class="text-light text-decoration-none">Terms of Service</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/donation-policy.php" class="text-light text-decoration-none">Donation Policy</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/refund-policy.php" class="text-light text-decoration-none">Refund & Cancellation</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/shipping-policy.php" class="text-light text-decoration-none">Shipping & Delivery</a></li>
                    <li class="mb-2"><a href="/Kamadhenu-goushala/sponsorship-terms.php" class="text-light text-decoration-none">Sponsorship Terms</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="font-ui text-white fw-bold mb-3">Sacred Sanctuary</h5>
                <p class="small text-light mb-2"><i class="fas fa-map-marker-alt text-warning me-2"></i> <?php echo e(get_setting($pdo, 'goushala_address', 'Kamadenu Grove, Nelamangala Road, Bengaluru Rural, Karnataka 562123')); ?></p>
                <p class="small text-light mb-2"><i class="fas fa-phone text-warning me-2"></i> <?php echo e(get_setting($pdo, 'contact_phone', '+91 98800 12345')); ?></p>
                <p class="small text-light mb-3"><i class="fas fa-envelope text-warning me-2"></i> <?php echo e(get_setting($pdo, 'contact_email', 'info@kamadenugoushala.org')); ?></p>

                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-warning btn-sm rounded-circle"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-warning btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-warning btn-sm rounded-circle"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="btn btn-outline-warning btn-sm rounded-circle"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center small">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                &copy; <?php echo date('Y'); ?> <?php echo __t('site_title'); ?>. <?php echo __t('footer_rights'); ?>
            </div>
            <div class="col-md-6 text-center text-md-end text-muted">
                Developed with devotion for Gouseva & A2 Preservation.
            </div>
        </div>
    </div>
</footer>

<!-- Interactive Cart Offcanvas Drawer -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header bg-dark text-white border-bottom border-warning">
        <h5 class="offcanvas-title font-heading text-warning" id="cartOffcanvasLabel">
            <i class="fas fa-shopping-basket me-2"></i> <?php echo __t('quick_cart'); ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
        <div id="drawer-cart-items-container" class="overflow-y-auto pe-1" style="max-height: 65vh;">
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shopping-basket fs-1 mb-2"></i>
                <p><?php echo __t('cart_empty'); ?></p>
            </div>
        </div>
        <div class="border-top pt-3 mt-3 bg-card p-3 rounded shadow-sm">
            <div class="d-flex justify-content-between font-ui mb-3 fs-5">
                <span class="fw-bold"><?php echo __t('cart_total'); ?>:</span>
                <strong id="drawer-cart-total" class="font-mono text-warning-dark">₹0.00</strong>
            </div>
            <div class="d-grid gap-2">
                <a href="/Kamadhenu-goushala/cart.php" class="btn btn-outline-dark font-ui fw-bold">
                    <i class="fas fa-shopping-cart me-1"></i> <?php echo __t('btn_view_cart'); ?>
                </a>
                <button onclick="proceedCartCheckout()" id="drawer-checkout-btn" class="btn btn-warning font-ui fw-bold py-2 shadow text-dark fs-6" disabled>
                    <i class="fas fa-bolt me-1"></i> <?php echo __t('btn_buy_now'); ?> / <?php echo __t('btn_proceed_checkout'); ?>
                </button>
            </div>
        </div>
    </div>
</div>


<?php
$page_file = basename($_SERVER['SCRIPT_NAME']);
?>

<!-- External Libraries: Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<?php if ($page_file === 'checkout.php'): ?>
    <!-- Razorpay Checkout loaded only on checkout page -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php endif; ?>

<!-- Application Scripts -->
<script src="/Kamadhenu-goushala/js/main.js"></script>
<script src="/Kamadhenu-goushala/js/language.js"></script>
<script src="/Kamadhenu-goushala/js/theme.js"></script>
<script src="/Kamadhenu-goushala/js/api.js"></script>
<script src="/Kamadhenu-goushala/js/realtime.js"></script>

</body>
</html>
