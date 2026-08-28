/**
 * KAMADENU GOUSHALA - MAIN APPLICATION JS
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Tooltips & Popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Cart Count Update
    updateCartBadge();

    // Initialize Scroll Glow Up Observer
    initScrollGlowAnimation();
});


// Toast Helper
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'danger' ? 'bg-danger text-white' : 'bg-warning text-dark');
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body font-ui">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHTML);
    const toastEl = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
    bsToast.show();

    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Auth Helper for User Actions
function requireUserAuth(actionDesc = 'perform this action', redirectTarget = null) {
    if (window.isUserLoggedIn) {
        return true;
    }
    const target = redirectTarget || (window.location.pathname + window.location.search);
    showToast(`Please login first to ${actionDesc}.`, 'warning');
    setTimeout(() => {
        window.location.href = `/Kamadhenu-goushala/login.php?redirect=${encodeURIComponent(target)}&msg=login_required`;
    }, 800);
    return false;
}

// Cart Management (LocalStorage Sync)
function getCart() {
    return JSON.parse(localStorage.getItem('kamadenu_cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('kamadenu_cart', JSON.stringify(cart));
    updateCartBadge();
    renderCartDrawer();
}

function addToCart(productId, name, price, image) {
    if (!requireUserAuth('add items to your cart', '/Kamadhenu-goushala/products.php')) {
        return;
    }
    let cart = getCart();
    let existing = cart.find(item => item.id == productId);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id: productId, name: name, price: price, image: image, quantity: 1 });
    }
    saveCart(cart);
    showToast(`Added ${name} to your cart!`, 'success');
    window.location.href = '/Kamadhenu-goushala/cart.php';
}

let currentBuyNowProduct = null;

function buyNow(productId, name, price, image, checkoutMethod, whatsappUrl) {
    if (!requireUserAuth('buy products and checkout', '/Kamadhenu-goushala/buy-product.php?id=' + productId)) {
        return;
    }

    checkoutMethod = checkoutMethod || 'both';

    if (checkoutMethod === 'website') {
        proceedWebsiteCheckout(productId, name, price, image);
    } else if (checkoutMethod === 'whatsapp') {
        if (whatsappUrl) {
            window.location.href = whatsappUrl;
        } else {
            window.location.href = '/Kamadhenu-goushala/buy-product.php?id=' + productId;
        }
    } else {
        window.location.href = '/Kamadhenu-goushala/buy-product.php?id=' + productId;
    }
}

function proceedWebsiteCheckout(productId, name, price, image) {
    if (!productId && currentBuyNowProduct) {
        productId = currentBuyNowProduct.productId;
        name = currentBuyNowProduct.name;
        price = currentBuyNowProduct.price;
        image = currentBuyNowProduct.image;
    }
    if (!productId) return;

    let cart = getCart();
    let existing = cart.find(item => item.id == productId);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id: productId, name: name, price: price, image: image, quantity: 1 });
    }
    saveCart(cart);

    const modalEl = document.getElementById('buyNowModal');
    if (modalEl) {
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();
    }

    window.location.href = '/Kamadhenu-goushala/checkout.php?type=cart';
}

function updateCartBadge() {
    const cart = getCart();
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(b => {
        b.textContent = totalCount;
        b.style.display = totalCount > 0 ? 'inline-block' : 'none';
    });
}

function openCartDrawer() {
    renderCartDrawer();
    const offcanvasEl = document.getElementById('cartOffcanvas');
    if (offcanvasEl) {
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
        bsOffcanvas.show();
    }
}

function toggleCartDrawer() {
    renderCartDrawer();
    const offcanvasEl = document.getElementById('cartOffcanvas');
    if (offcanvasEl) {
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
        bsOffcanvas.toggle();
    }
}

function renderCartDrawer() {
    const container = document.getElementById('drawer-cart-items-container');
    const totalEl = document.getElementById('drawer-cart-total');
    const checkoutBtn = document.getElementById('drawer-checkout-btn');
    if (!container) return;

    const cart = getCart();
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shopping-basket fs-1 mb-2 d-block text-warning"></i>
                <p class="font-ui fw-bold mb-1">Your cart is empty.</p>
                <small>Add Goushala products to support Gouseva.</small>
            </div>
        `;
        if (totalEl) totalEl.textContent = '₹0.00';
        if (checkoutBtn) checkoutBtn.disabled = true;
        return;
    }

    let html = '<div class="list-group list-group-flush">';
    let total = 0;

    cart.forEach(item => {
        const sub = item.price * item.quantity;
        total += sub;
        html += `
            <div class="list-group-item px-0 py-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.image}" width="50" height="50" class="rounded object-fit-cover shadow-sm" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=100&q=80'">
                    <div class="flex-grow-1">
                        <h6 class="font-ui fw-bold mb-1 text-dark fs-6">${item.name}</h6>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-mono text-muted small">₹${item.price.toLocaleString('en-IN')} × ${item.quantity}</span>
                            <span class="font-mono fw-bold text-dark">₹${sub.toLocaleString('en-IN')}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light">
                    <div class="input-group input-group-sm" style="width: 90px;">
                        <button class="btn btn-outline-secondary btn-sm" onclick="changeDrawerQty(${item.id}, -1)">-</button>
                        <input type="text" class="form-control text-center font-mono p-0" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" onclick="changeDrawerQty(${item.id}, 1)">+</button>
                    </div>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeDrawerItem(${item.id})"><i class="fas fa-trash-alt me-1"></i> Remove</button>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;

    if (totalEl) totalEl.textContent = '₹' + total.toLocaleString('en-IN');
    if (checkoutBtn) checkoutBtn.disabled = false;
}

function changeDrawerQty(id, delta) {
    let cart = getCart();
    let item = cart.find(i => i.id == id);
    if (item) {
        item.quantity += delta;
        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id != id);
        }
        saveCart(cart);
        if (typeof renderCartPage === 'function') renderCartPage();
    }
}

function removeDrawerItem(id) {
    let cart = getCart();
    cart = cart.filter(i => i.id != id);
    saveCart(cart);
    if (typeof renderCartPage === 'function') renderCartPage();
}

function proceedCartCheckout() {
    const cart = getCart();
    if (cart.length === 0) {
        showToast('Your cart is empty!', 'warning');
        return;
    }
    const total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    window.location.href = `/Kamadhenu-goushala/checkout.php?type=cart&amount=${total}`;
}

// Scroll-Triggered Glow Up Observer for Photo Divs & Cards (Ultra Smooth)
function initScrollGlowAnimation() {
    const targets = document.querySelectorAll('.kamadenu-card, .hero-cow-kamala-card, .cow-card-img, img.rounded-4, img.rounded-3, .scroll-glow-target');
    if (!targets.length) return;

    targets.forEach((el) => {
        el.classList.add('scroll-glow-item');
        // Faster, subtle stagger delay
        const col = el.closest('[class*="col-"]');
        if (col && col.parentNode) {
            const index = Array.from(col.parentNode.children).indexOf(col);
            if (index >= 0) {
                el.style.transitionDelay = `${(index % 4) * 0.05}s`;
            }
        }
    });

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('scroll-glow-active');
                obs.unobserve(entry.target); // Unobserve once animated for 60fps performance
            }
        });
    }, {
        root: null,
        rootMargin: '0px 0px -15px 0px',
        threshold: 0.05
    });

    targets.forEach(target => observer.observe(target));
}

// Floating Golden Sparks Background Particle Animation
function initHeroParticles() {
    const canvas = document.getElementById('hero-particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width = canvas.width = canvas.offsetWidth;
    let height = canvas.height = canvas.offsetHeight;

    window.addEventListener('resize', () => {
        if (!canvas) return;
        width = canvas.width = canvas.offsetWidth;
        height = canvas.height = canvas.offsetHeight;
    });

    const particles = [];
    const count = 35; // Lightweight 60fps performance count

    for (let i = 0; i < count; i++) {
        particles.push({
            x: Math.random() * width,
            y: Math.random() * height,
            radius: Math.random() * 2.2 + 0.8,
            speedY: Math.random() * 0.4 + 0.15,
            speedX: (Math.random() - 0.5) * 0.2,
            opacity: Math.random() * 0.7 + 0.2,
            pulseSpeed: Math.random() * 0.03 + 0.01
        });
    }

    function render() {
        ctx.clearRect(0, 0, width, height);

        particles.forEach(p => {
            p.y -= p.speedY;
            p.x += p.speedX;
            p.opacity += Math.sin(Date.now() * p.pulseSpeed) * 0.005;

            if (p.y < -10) {
                p.y = height + 10;
                p.x = Math.random() * width;
            }

            ctx.fillStyle = `rgba(245, 158, 11, ${Math.max(0.1, Math.min(0.8, p.opacity))})`;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fill();
        });

        requestAnimationFrame(render);
    }

    render();
}

// Interactive 3D Cursor Tilt Parallax Effect for Cards
function init3DTiltEffect() {
    const tiltCards = document.querySelectorAll('.kamadenu-card, .hero-cow-kamala-card, .admin-stat-box, .admin-card');
    if (!tiltCards.length) return;

    tiltCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -7; // Max 7 deg tilt
            const rotateY = ((x - centerX) / centerX) * 7;   // Max 7 deg tilt

            card.style.transform = `perspective(1000px) translate3d(0, -6px, 15px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) translate3d(0, 0, 0) rotateX(0deg) rotateY(0deg)';
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('page-3d-entrance');
    initHeroParticles();
    init3DTiltEffect();
    initScrollGlowAnimation();
});






