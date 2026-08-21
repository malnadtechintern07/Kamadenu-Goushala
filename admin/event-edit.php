<?php
require_once __DIR__ . '/header.php';

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

    $stmt = $pdo->prepare("UPDATE events SET title = ?, title_kn = ?, description = ?, event_date = ?, venue = ?, photo = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $title_kn, $description, $event_date, $venue, $photo_url, $status, $id]);

    log_audit($pdo, 'Edit Event', 'events', $id);
    header("Location: /Kamadenu/admin/events.php?updated=1");
    exit;
}
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

            <!-- Current Photo Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                    <img src="<?php echo img_url($ev['photo']); ?>" width="120" height="80" class="rounded object-fit-cover shadow-sm">
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
