<?php
require_once __DIR__ . '/includes/header.php';

if (is_user_logged_in()) {
    header("Location: /Kamadenu/dashboard.php");
    exit;
}
?>

<section class="py-5 bg-card">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="kamadenu-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle text-warning display-4 mb-2"></i>
                        <h3 class="font-heading mb-1">User Login</h3>
                        <p class="text-muted small">Access your Gouseva dashboard, certificates & cow updates</p>
                    </div>

                    <div class="alert bg-warning-subtle text-dark border border-warning small mb-4">
                        <i class="fas fa-key me-1 text-warning"></i> <strong>Demo Credentials:</strong><br>
                        Email: <code>user@kamadenugoushala.org</code><br>
                        Password: <code>user123</code>
                    </div>

                    <form id="login-form">
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="user@kamadenugoushala.org" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow">Login to Account</button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3 small">
                        Don't have an account? <a href="/Kamadenu/register.php" class="text-warning fw-bold">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch('/Kamadenu/api/auth.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            email: this.email.value,
            password: this.password.value
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.href = res.data.redirect, 1000);
        } else {
            showToast(res.message, 'danger');
            btn.disabled = false;
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
