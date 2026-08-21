<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $title_kn = trim($_POST['title_kn']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $venue = trim($_POST['venue']);
    $status = $_POST['status'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();

    $photo_url = handle_file_upload('photo_file', trim($_POST['photo_url']));

    $stmt = $pdo->prepare("INSERT INTO events (title, title_kn, slug, description, event_date, venue, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $title_kn, $slug, $description, $event_date, $venue, $photo_url, $status]);

    log_audit($pdo, 'Create Event', 'events', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/events.php?saved=1");
    exit;
}
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
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold"><i class="fas fa-upload text-warning me-1"></i> Upload Event Photo (File)</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
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
