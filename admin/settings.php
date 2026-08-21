<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $val) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([trim($val), $key]);
    }
    log_audit($pdo, 'Update System Settings', 'settings');
    header("Location: /Kamadenu/admin/settings.php?saved=1");
    exit;
}

$settings = $pdo->query("SELECT * FROM settings ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-cog text-warning me-2"></i> System & Razorpay Payment Settings</h3>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">System settings saved to MySQL.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-4">
            <?php foreach ($settings as $s): ?>
                <div class="col-md-6">
                    <label class="form-label font-ui small fw-bold"><?php echo e(ucwords(str_replace('_', ' ', $s['setting_key']))); ?></label>
                    <input type="text" name="settings[<?php echo e($s['setting_key']); ?>]" class="form-control font-mono" value="<?php echo e($s['setting_value']); ?>" required>
                    <small class="text-muted"><?php echo e($s['description']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Save Settings</button>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
