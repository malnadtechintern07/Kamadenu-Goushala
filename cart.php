<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo __t('cart_title'); ?></h1>
        <p class="text-white-50 mb-0"><?php echo __t('cart_subtitle'); ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="kamadenu-card p-4">
                    <h4 class="font-heading mb-4"><i class="fas fa-shopping-basket me-2 text-warning"></i> <?php echo __t('cart_items'); ?></h4>
                    <div id="cart-items-container">
                        <div class="text-center py-4 text-muted">Loading your cart...</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="kamadenu-card p-4 sticky-top" style="top: 100px;">
                    <h4 class="font-heading mb-3"><?php echo __t('cart_summary'); ?></h4>
                    <div class="d-flex justify-content-between py-2 border-bottom font-ui">
                        <span><?php echo __t('cart_subtotal'); ?></span>
                        <strong id="cart-subtotal" class="font-mono">₹0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom font-ui">
                        <span><?php echo __t('cart_shipping'); ?></span>
                        <span class="text-success font-ui fw-bold"><?php echo __t('cart_free'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-3 fs-4 font-heading text-dark">
                        <span><?php echo __t('cart_total'); ?></span>
                        <strong id="cart-total" class="font-mono text-warning-dark">₹0.00</strong>
                    </div>

                    <button id="cart-checkout-btn" onclick="proceedCartCheckout()" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow" disabled>
                        <i class="fas fa-lock me-2"></i> <?php echo __t('btn_proceed_checkout'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', renderCartPage);

function renderCartPage() {
    const container = document.getElementById('cart-items-container');
    const cart = getCart();

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fs-1 text-muted mb-3 d-block"></i>
                <h5>Your cart is empty.</h5>
                <a href="/Kamadenu/products.php" class="btn btn-warning rounded-pill mt-2 font-ui fw-bold">Browse Store</a>
            </div>
        `;
        document.getElementById('cart-subtotal').textContent = '₹0.00';
        document.getElementById('cart-total').textContent = '₹0.00';
        document.getElementById('cart-checkout-btn').disabled = true;
        return;
    }

    let html = '<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>';
    let total = 0;

    cart.forEach(item => {
        const sub = item.price * item.quantity;
        total += sub;
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <img src="${item.image}" width="50" height="50" class="rounded object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=100&q=80'">
                        <strong class="font-ui">${item.name}</strong>
                    </div>
                </td>
                <td class="font-mono">₹${item.price.toLocaleString('en-IN')}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button class="btn btn-outline-secondary" onclick="changeQty(${item.id}, -1)">-</button>
                        <input type="text" class="form-control text-center font-mono" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary" onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td class="font-mono fw-bold">₹${sub.toLocaleString('en-IN')}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;

    document.getElementById('cart-subtotal').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('cart-total').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('cart-checkout-btn').disabled = false;
}

function changeQty(id, delta) {
    let cart = getCart();
    let item = cart.find(i => i.id === id);
    if (item) {
        item.quantity += delta;
        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        saveCart(cart);
        renderCartPage();
    }
}

function removeItem(id) {
    let cart = getCart();
    cart = cart.filter(i => i.id !== id);
    saveCart(cart);
    renderCartPage();
}

function proceedCartCheckout() {
    const cart = getCart();
    const total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    window.location.href = `/Kamadenu/checkout.php?type=cart&amount=${total}`;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
