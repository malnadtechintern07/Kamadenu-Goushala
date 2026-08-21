<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    // Send broadcast to all active users
    $users = $pdo->query("SELECT id FROM users WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'info')");
    foreach ($users as $uid) {
        $stmt->execute([$uid, $title, $message]);
    }

    log_audit($pdo, 'Broadcast Notification', 'notifications');
    header("Location: /Kamadenu/admin/notifications.php?sent=1");
    exit;
}

$notifications = $pdo->query("SELECT n.*, u.name as user_name FROM notifications n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.id DESC LIMIT 30")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-bell text-warning me-2"></i> Broadcast Notifications System</h3>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Send Broadcast Alert</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Notification Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Monthly Cow Health Update Available" required>
                </div>
                <div class="mb-4">
                    <label class="form-label font-ui small fw-bold">Message Content</label>
                    <textarea name="message" rows="4" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">Send Notification</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Recent System Alerts</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notifications as $n): ?>
                            <tr>
                                <td><strong><?php echo $n['user_name'] ? e($n['user_name']) : 'All Users'; ?></strong></td>
                                <td><?php echo e($n['title']); ?></td>
                                <td><span class="badge <?php echo $n['is_read'] ? 'bg-secondary' : 'bg-warning text-dark'; ?>"><?php echo $n['is_read'] ? 'Read' : 'Unread'; ?></span></td>
                                <td class="font-mono"><?php echo date('M d, H:i', strtotime($n['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
