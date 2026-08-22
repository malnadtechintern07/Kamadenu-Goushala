<?php
require_once __DIR__ . '/config/database.php';

if (is_user_logged_in()) {
    header("Location: /Kamadenu/dashboard.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-card">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="kamadenu-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus text-warning display-4 mb-2"></i>
                        <h3 class="font-heading mb-1">Create Account</h3>
                        <p class="text-muted small">Join Kamadenu Gouseva & receive 50 Welcome Points</p>
                    </div>

                    <form id="register-form">
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Your Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 Phone Number" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Create Password" required>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow">Register & Join Gouseva</button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3 small">
                        Already registered? <a href="/Kamadenu/login.php" class="text-warning fw-bold">Login Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch('/Kamadenu/api/auth.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: this.name.value,
            email: this.email.value,
            phone: this.phone.value,
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
