<?php
require_once __DIR__ . '/config/database.php';

// If user is already logged in, send them to dashboard or their intended target URL
$redirect_target = isset($_GET['redirect']) ? $_GET['redirect'] : '/Kamadenu/dashboard.php';

if (is_user_logged_in()) {
    header("Location: " . $redirect_target);
    exit;
}

require_once __DIR__ . '/includes/header.php';
$show_login_msg = isset($_GET['msg']) && $_GET['msg'] === 'login_required';
?>

<section class="py-5 bg-gradient-dark min-vh-75 d-flex align-items-center position-relative overflow-hidden">
    <!-- Subtle Background Glow Elements -->
    <div class="position-absolute top-0 start-50 translate-middle-x rounded-circle" style="width: 500px; height: 500px; background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, rgba(0,0,0,0) 70%); pointer-events: none;"></div>

    <div class="container py-4 position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="kamadenu-card p-4 p-md-5 border-warning shadow-lg rounded-4 position-relative" style="background: rgba(255, 255, 255, 0.97); backdrop-filter: blur(12px);">
                    
                    <?php if ($show_login_msg): ?>
                        <div class="alert alert-warning border-warning shadow-sm font-ui mb-4 rounded-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-lock fs-4 text-warning me-3"></i>
                                <div>
                                    <strong class="d-block text-dark">Please Login First</strong>
                                    <span class="small text-muted">An account is required to perform this action or complete checkout.</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Devotional Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="/Kamadenu/assets/images/logo.png" alt="Kamadenu Goushala Trust Logo" class="rounded-circle shadow-sm" style="height: 72px; width: 72px; object-fit: contain; background: #FFF8EA; padding: 6px; border: 2px solid var(--brand-gold);">
                        </div>
                        <div class="devotional-phrase text-warning fs-5 fw-bold mb-1">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                        <h2 class="font-heading mb-1 text-dark">Welcome to Gouseva</h2>
                        <p class="text-muted small font-ui">Access your Gouseva dashboard, certificates &amp; cow updates</p>
                    </div>

                    <!-- Login Form -->
                    <form id="login-form">
                        <input type="hidden" name="redirect" value="<?php echo e($redirect_target); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold text-dark">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-warning-subtle text-warning"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg font-ui" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold text-dark">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-warning-subtle text-warning"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="login-password-field" class="form-control form-control-lg font-ui" placeholder="Enter password" required>
                                <button type="button" class="btn btn-outline-secondary border-warning-subtle" onclick="togglePasswordVisibility('login-password-field', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow rounded-pill d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login to Account</span>
                        </button>
                    </form>

                    <!-- Footer Link -->
                    <div class="text-center mt-4 border-top pt-3 font-ui small">
                        <span class="text-muted">Don't have an account yet?</span>
                        <a href="/Kamadenu/register.php?redirect=<?php echo urlencode($redirect_target); ?>" class="text-warning-dark fw-bold ms-1 text-decoration-none">Register Now &rarr;</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Logging in...';

    fetch('/Kamadenu/api/auth.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            email: this.email.value,
            password: this.password.value,
            redirect: this.redirect.value
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.href = res.data.redirect || '/Kamadenu/dashboard.php', 800);
        } else {
            showToast(res.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Login server request failed.', 'danger');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
