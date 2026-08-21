<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php"); exit; }
$user = current_user($pdo);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$notifications = $stmt->fetchAll();

// Mark notifications as read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user['id']]);
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">Notifications & Updates</h1>
        <p class="text-white-50 mb-0">System updates, payment receipts, and monthly cow reports.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="kamadenu-card p-4">
            <?php if (empty($notifications)): ?>
                <p class="text-muted text-center py-4">No notifications.</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $n): ?>
                        <div class="list-group-item p-3 border-bottom">
                            <h5 class="font-heading text-dark mb-1"><?php echo e($n['title']); ?></h5>
                            <p class="small text-muted mb-1"><?php echo e($n['message']); ?></p>
                            <small class="font-mono text-muted"><?php echo date('M d, Y H:i', strtotime($n['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
