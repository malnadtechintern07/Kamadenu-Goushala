<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM feeding_cows WHERE id = ?");
$stmt->execute([$id]);
$cow = $stmt->fetch();

if (!$cow) {
    require_once __DIR__ . '/header.php';
    echo "<div class='container mt-5'><div class='alert alert-danger'>Feeding cow record not found.</div></div>";
    require_once __DIR__ . '/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $feed_amount = floatval($_POST['feed_amount']);
    $is_available = isset($_POST['is_available']) ? intval($_POST['is_available']) : 1;
    $payment_method = trim($_POST['payment_method']);
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;

    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    } else {
        $photo_path = $cow['photo'];
    }

    $stmt = $pdo->prepare("UPDATE feeding_cows SET name = ?, description = ?, photo = ?, feed_amount = ?, is_available = ?, payment_method = ?, whatsapp_number_id = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$name, $description, $photo_path, $feed_amount, $is_available, $payment_method, $whatsapp_number_id, $whatsapp_message, $id]);

    log_audit($pdo, 'Edit Feeding Cow', 'feeding_cows', $id);
    header("Location: /Kamadenu/admin/feed-cows.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Feeding Cow (<?php echo e($cow['cow_code']); ?>)</h3>
    <a href="/Kamadenu/admin/feed-cows.php" class="btn btn-outline-secondary font-ui">&larr; Back to List</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Unique Code</label>
                <input type="text" class="form-control font-mono" value="<?php echo e($cow['cow_code']); ?>" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($cow['name']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Suggested Feeding Amount (INR)</label>
                <input type="number" name="feed_amount" class="form-control font-mono" value="<?php echo $cow['feed_amount']; ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Availability Status</label>
                <select name="is_available" class="form-select">
                    <option value="1" <?php echo $cow['is_available'] == 1 ? 'selected' : ''; ?>>Available (Visible to Public)</option>
                    <option value="0" <?php echo $cow['is_available'] == 0 ? 'selected' : ''; ?>>Hidden / Unavailable</option>
                </select>
            </div>

            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> Payment Action & WhatsApp Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Payment Method Mode</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="website" <?php echo $cow['payment_method'] === 'website' ? 'selected' : ''; ?>>Website Gateway Payment Only</option>
                                <option value="whatsapp" <?php echo $cow['payment_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Payment (Direct Message)</option>
                                <option value="both" <?php echo $cow['payment_method'] === 'both' ? 'selected' : ''; ?>>Both Options (Show Website Checkout & WhatsApp to User)</option>
                            </select>
                            <small class="text-muted">Choose how user payments are processed for this cow.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Support Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($cow['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global WhatsApp support number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($cow['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I want to feed this cow. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp input field when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Cow Photo Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                    <img src="<?php echo img_url($cow['photo']); ?>" class="rounded shadow-sm" style="width: 100px; height: 80px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                    <div>
                        <strong class="d-block font-heading">Current Active Cow Photo</strong>
                        <small class="text-muted font-mono d-block"><?php echo e($cow['photo']); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload New Photo</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" value="<?php echo e($cow['photo']); ?>">
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Description / Story (English)</label>
                <textarea name="description" rows="4" class="form-control" required><?php echo e($cow['description']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2 text-white">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
        <a href="/Kamadenu/admin/feed-cows.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
