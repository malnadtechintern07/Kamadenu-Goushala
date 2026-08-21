<?php require_once __DIR__ . '/includes/header.php'; ?>
<section class="py-5 bg-card">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="kamadenu-card p-4 p-md-5 text-center">
                    <i class="fas fa-key text-warning display-4 mb-3"></i>
                    <h3 class="font-heading mb-2">Password Recovery</h3>
                    <p class="text-muted small mb-4">Enter your registered email address to receive password reset instructions.</p>
                    <form onsubmit="event.preventDefault(); showToast('Password reset link has been dispatched to your email.', 'success');">
                        <div class="mb-4 text-start">
                            <label class="form-label font-ui small fw-bold">Email Address</label>
                            <input type="email" class="form-control form-control-lg" placeholder="user@kamadenugoushala.org" required>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
