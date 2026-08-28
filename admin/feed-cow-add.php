<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cow_code = trim($_POST['cow_code']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $feed_amount = floatval($_POST['feed_amount']);
    $is_available = isset($_POST['is_available']) ? intval($_POST['is_available']) : 1;
    $payment_method = trim($_POST['payment_method']);
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;

    // Handle photo upload
    $photo_path = 'assets/images/cow-default.jpg';
    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    }

    $stmt = $pdo->prepare("INSERT INTO feeding_cows (cow_code, name, description, photo, feed_amount, is_available, payment_method, whatsapp_number_id, whatsapp_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cow_code, $name, $description, $photo_path, $feed_amount, $is_available, $payment_method, $whatsapp_number_id, $whatsapp_message]);

    log_audit($pdo, 'Add Feeding Cow', 'feeding_cows', $pdo->lastInsertId());
    header("Location: /Kamadhenu-goushala/admin/feed-cows.php?saved=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-plus-circle text-warning me-2"></i> Register Feeding Cow</h3>
    <a href="/Kamadhenu-goushala/admin/feed-cows.php" class="btn btn-outline-secondary font-ui">&larr; Back to List</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Unique Code (e.g. FC-001)</label>
                <input type="text" name="cow_code" class="form-control font-mono" placeholder="FC-001" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Radha" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Suggested Feeding Amount (INR)</label>
                <input type="number" name="feed_amount" class="form-control font-mono" value="500" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Option 1: Upload Cow Photo</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Option 2: Enter Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" placeholder="https://...">
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Availability Status</label>
                <select name="is_available" class="form-select">
                    <option value="1">Available (Visible to Public)</option>
                    <option value="0">Hidden / Unavailable</option>
                </select>
            </div>

            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> Payment Action & WhatsApp Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Payment Method Mode</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="website">Website Gateway Payment Only</option>
                                <option value="whatsapp">WhatsApp Payment (Direct Message)</option>
                                <option value="both" selected>Both Options (Show Website Checkout & WhatsApp to User)</option>
                            </select>
                            <small class="text-muted">Determines which payment action buttons are displayed to the user.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Support Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>">
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global WhatsApp support number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" placeholder="e.g. Hare Krishna! I want to feed this cow. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp input field when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Description / Story (English)</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Describe the cow or feeding necessity..." required></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Save Feeding Cow</button>
        <a href="/Kamadhenu-goushala/admin/feed-cows.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
