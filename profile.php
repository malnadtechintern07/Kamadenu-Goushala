<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadhenu-goushala/login.php"); exit; }
$user = current_user($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $address, $user['id']]);
    $_SESSION['user_name'] = $name;
    header("Location: /Kamadhenu-goushala/profile.php?saved=1");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Account Profile</h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <?php if (isset($_GET['saved'])): ?>
                    <div class="alert alert-success">Profile details updated successfully!</div>
                <?php endif; ?>
                <div class="kamadenu-card p-4 p-md-5">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo e($user['email']); ?>" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo e($user['phone']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold">Mailing Address</label>
                            <textarea name="address" rows="3" class="form-control"><?php echo e($user['address']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5">Save Profile Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
