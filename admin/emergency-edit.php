<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM emergency_campaigns WHERE id = ?");
$stmt->execute([$id]);
$campaign = $stmt->fetch();

if (!$campaign) {
    header("Location: /Kamadenu/admin/emergency.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $story = trim($_POST['story']);
    $target_amount = floatval($_POST['target_amount']);
    $urgency_level = $_POST['urgency_level'];
    $status = $_POST['status'];
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;

    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    } else {
        $photo_path = $campaign['photo'];
    }

    $stmt = $pdo->prepare("UPDATE emergency_campaigns SET title = ?, story = ?, target_amount = ?, urgency_level = ?, status = ?, photo = ?, whatsapp_number_id = ?, contact_method = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$title, $story, $target_amount, $urgency_level, $status, $photo_path, $whatsapp_number_id, $contact_method, $whatsapp_message, $id]);

    log_audit($pdo, 'Edit Emergency Campaign', 'emergency_campaigns', $id);
    header("Location: /Kamadenu/admin/emergency.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-edit text-danger me-2"></i> Edit Rescue Campaign & Photo</h3>
    <a href="/Kamadenu/admin/emergency.php" class="btn btn-outline-secondary font-ui">&larr; Back to Campaigns</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label font-ui small fw-bold">Campaign Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($campaign['title']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Target Funding Goal (INR)</label>
                <input type="number" step="0.01" name="target_amount" class="form-control font-mono" value="<?php echo $campaign['target_amount']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Urgency Level</label>
                <select name="urgency_level" class="form-select">
                    <option value="Critical" <?php echo $campaign['urgency_level'] === 'Critical' ? 'selected' : ''; ?>>Critical</option>
                    <option value="High" <?php echo $campaign['urgency_level'] === 'High' ? 'selected' : ''; ?>>High</option>
                    <option value="Normal" <?php echo $campaign['urgency_level'] === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Campaign Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?php echo $campaign['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Paused" <?php echo $campaign['status'] === 'Paused' ? 'selected' : ''; ?>>Paused</option>
                    <option value="Completed" <?php echo $campaign['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <!-- Spacer -->
            </div>

            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" <?php echo $campaign['contact_method'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $campaign['contact_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Contact (Direct Message)</option>
                                <option value="both" <?php echo $campaign['contact_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted">Choose how user payments are processed for this campaign.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Order Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($campaign['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($campaign['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I want to donate to the emergency rescue campaign. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Campaign Photo Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                     <img src="<?php echo img_url($campaign['photo']); ?>" class="rounded shadow-sm" style="width: 100px; height: 80px; object-fit: cover; flex-shrink: 0;">
                    <div>
                        <strong class="d-block font-heading">Current Rescue Campaign Photo</strong>
                        <small class="text-muted font-mono d-block"><?php echo e($campaign['photo']); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload New Campaign Photo File</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" value="<?php echo e($campaign['photo']); ?>">
            </div>



            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Rescue Story & Emergency Details</label>
                <textarea name="story" rows="4" class="form-control" required><?php echo e($campaign['story']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-danger font-ui fw-bold px-4 py-2">
            <i class="fas fa-save me-1"></i> Update Campaign & Photo in MySQL
        </button>
        <a href="/Kamadenu/admin/emergency.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
