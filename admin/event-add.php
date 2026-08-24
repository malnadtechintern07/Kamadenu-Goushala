<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $title_kn = trim($_POST['title_kn']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $venue = trim($_POST['venue']);
    $status = $_POST['status'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;

    $photo_url = handle_file_upload('photo_file', trim($_POST['photo_url']));

    $stmt = $pdo->prepare("INSERT INTO events (title, title_kn, slug, description, event_date, venue, photo, status, whatsapp_number_id, contact_method, whatsapp_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $title_kn, $slug, $description, $event_date, $venue, $photo_url, $status, $whatsapp_number_id, $contact_method, $whatsapp_message]);

    log_audit($pdo, 'Create Event', 'events', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/events.php?saved=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<h3 class="font-heading mb-4"><i class="fas fa-calendar-plus text-warning me-2"></i> Add New Trust Event</h3>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Event Title (English)</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Gopashtami Mahotsav">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Event Title (Kannada)</label>
                <input type="text" name="title_kn" class="form-control kn-text" placeholder="ಉದಾ. ಗೋಪಾಷ್ಟಮಿ ಮಹೋತ್ಸವ">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Event Date</label>
                <input type="date" name="event_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Venue</label>
                <input type="text" name="venue" class="form-control" value="Kamadenu Goushala Sanctuary, Bengaluru" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="Upcoming">Upcoming</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold"><i class="fas fa-upload text-warning me-1"></i> Upload Event Photo (File)</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>

            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" selected>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp">WhatsApp Contact (Direct Message)</option>
                                <option value="both">Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted">Choose how user bookings are processed for this event.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Order Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>">
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" placeholder="e.g. Hare Krishna! I want to join the trust event. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label font-ui small fw-bold">Or Image URL</label>
                <input type="text" name="photo_url" class="form-control" placeholder="https://...">
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Event Description & Program Schedule</label>
                <textarea name="description" rows="5" class="form-control" required></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Create Event in MySQL</button>
        <a href="/Kamadenu/admin/events.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
