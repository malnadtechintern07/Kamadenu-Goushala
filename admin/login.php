<?php
require_once __DIR__ . '/../config/database.php';

if (is_admin_logged_in()) {
    header("Location: /Kamadenu/admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal Login | Kamadenu Goushala</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Kamadenu/css/style.css">
    
    <!-- Favicon Icon -->
    <link rel="icon" type="image/svg+xml" href="/Kamadenu/assets/images/favicon.svg">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="kamadenu-card p-4 p-md-5 shadow-lg border-warning">
                <div class="text-center mb-4">
                    <i class="fas fa-user-shield text-warning display-3 mb-2"></i>
                    <h2 class="font-heading text-dark mb-1">Goushala Admin Portal</h2>
                    <p class="text-muted small">Authorized Management Personnel Only</p>
                </div>

                <form id="admin-login-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label font-ui small fw-bold">Admin Email</label>
                        <input type="text" name="email" class="form-control form-control-lg" placeholder="abc@123" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-ui small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow">Authenticate Admin</button>
                </form>

                <div class="text-center mt-4">
                    <a href="/Kamadenu/index.php" class="text-muted small text-decoration-none">&larr; Return to Public Website</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/Kamadenu/js/main.js"></script>
<script>
document.getElementById('admin-login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch('/Kamadenu/api/auth.php?action=admin_login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'admin_login',
            email: this.email.value,
            password: this.password.value
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            window.location.href = res.data.redirect;
        } else {
            showToast(res.message, 'danger');
            btn.disabled = false;
        }
    });
});
</script>
</body>
</html>
