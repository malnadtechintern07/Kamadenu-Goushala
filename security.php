<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php"); exit; }
$user = current_user($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'];
    $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user['id']]);
    header("Location: /Kamadenu/security.php?updated=1");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container"><h1 class="font-heading text-warning">Account Security</h1></div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success">Password updated successfully!</div>
                <?php endif; ?>
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4">Change Password</h3>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-lg" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
