<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->execute([$id]);
$video = $stmt->fetch();

if (!$video) {
    echo "<div class='alert alert-danger'>Video record not found.</div>";
    require_once __DIR__ . '/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $youtube_url = trim($_POST['youtube_url']);

    $stmt = $pdo->prepare("UPDATE videos SET title = ?, description = ?, youtube_url = ? WHERE id = ?");
    $stmt->execute([$title, $description, $youtube_url, $id]);

    log_audit($pdo, 'Update Video', 'videos', $id);
    header("Location: /Kamadenu/admin/videos.php?updated=1");
    exit;
}
?>

<h3 class="font-heading mb-4"><i class="fab fa-youtube text-danger me-2"></i> Edit Program Video</h3>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Video Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($video['title']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">YouTube Video URL</label>
                <input type="url" name="youtube_url" class="form-control" value="<?php echo e($video['youtube_url']); ?>" required>
                <small class="text-muted">Accepts standard YouTube links or youtu.be shortened links.</small>
            </div>
            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Video Description</label>
                <textarea name="description" rows="4" class="form-control"><?php echo e($video['description']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Update Video</button>
        <a href="/Kamadenu/admin/videos.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
