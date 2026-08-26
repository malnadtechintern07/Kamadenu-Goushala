<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$ev = $stmt->fetch();

if (!$ev) {
    header("Location: /Kamadenu/admin/events.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $title_kn = trim($_POST['title_kn']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $venue = trim($_POST['venue']);
    $status = $_POST['status'];
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;

    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    // Priority: 1. Uploaded File, 2. Text URL, 3. Keep Existing Photo
    if (!empty($uploaded_photo)) {
        $photo_url = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_url = $url_photo;
    } else {
        $photo_url = $ev['photo'];
    }

    $stmt = $pdo->prepare("UPDATE events SET title = ?, title_kn = ?, description = ?, event_date = ?, venue = ?, photo = ?, status = ?, whatsapp_number_id = ?, contact_method = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$title, $title_kn, $description, $event_date, $venue, $photo_url, $status, $whatsapp_number_id, $contact_method, $whatsapp_message, $id]);

    log_audit($pdo, 'Edit Event', 'events', $id);
    header("Location: /Kamadenu/admin/events.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Event & Update Photo</h3>
    <a href="/Kamadenu/admin/events.php" class="btn btn-outline-secondary font-ui">&larr; Back to Events</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Event Title (English)</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($ev['title']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Event Title (Kannada)</label>
                <input type="text" name="title_kn" class="form-control kn-text" value="<?php echo e($ev['title_kn']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Event Date</label>
                <input type="date" name="event_date" class="form-control" value="<?php echo e($ev['event_date']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Venue</label>
                <input type="text" name="venue" class="form-control" value="<?php echo e($ev['venue']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="Upcoming" <?php echo $ev['status'] === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Ongoing" <?php echo $ev['status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo $ev['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" <?php echo $ev['contact_method'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $ev['contact_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Contact (Direct Message)</option>
                                <option value="both" <?php echo $ev['contact_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted">Choose how user bookings are processed for this event.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Order Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($ev['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($ev['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I want to join the trust event. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Photo Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                    <img src="<?php echo img_url($ev['photo']); ?>" class="rounded shadow-sm" style="width: 120px; height: 80px; object-fit: cover; flex-shrink: 0;">
                    <div>
                        <strong class="d-block font-heading">Current Active Event Photo</strong>
                        <small class="text-muted font-mono d-block"><?php echo e($ev['photo']); ?></small>
                        <span class="badge bg-success font-ui">Active Image</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload New Photo File</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
                <small class="text-muted">Upload JPG, PNG, WEBP file from your device.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" placeholder="https://..." value="<?php echo e($ev['photo']); ?>">
                <small class="text-muted">Or paste direct image URL (e.g. Unsplash, Web URL).</small>
            </div>



            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Event Description</label>
                <textarea name="description" rows="5" class="form-control" required><?php echo e($ev['description']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2 text-white">
            <i class="fas fa-save me-1"></i> Save Changes & Update Photo in MySQL
        </button>
        <a href="/Kamadenu/admin/events.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
