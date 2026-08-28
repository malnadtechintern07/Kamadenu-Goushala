<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $youtube_url = trim($_POST['youtube_url']);

    $stmt = $pdo->prepare("INSERT INTO videos (title, description, youtube_url) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $youtube_url]);

    log_audit($pdo, 'Create Video', 'videos', $pdo->lastInsertId());
    header("Location: /Kamadhenu-goushala/admin/videos.php?saved=1");
    exit;
}
?>

<h3 class="font-heading mb-4"><i class="fab fa-youtube text-danger me-2"></i> Add New Program Video</h3>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Video Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Daily Feeding Program">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">YouTube Video URL</label>
                <input type="url" name="youtube_url" class="form-control" required placeholder="e.g. https://www.youtube.com/watch?v=XmueYxEL6dg">
                <small class="text-muted">Accepts standard YouTube links or youtu.be shortened links.</small>
            </div>
            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Video Description</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Provide a brief summary of what is happening in this video."></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Save Video</button>
        <a href="/Kamadhenu-goushala/admin/videos.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
