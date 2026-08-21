<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();
    $pub_date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO stories (title, slug, summary, content, author, status, published_at) VALUES (?, ?, ?, ?, 'Kamadenu Team', 'Published', ?)");
    $stmt->execute([$title, $slug, $summary, $content, $pub_date]);

    log_audit($pdo, 'Create Story', 'stories', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/stories.php?saved=1");
    exit;
}

$stories = $pdo->query("SELECT * FROM stories ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-newspaper text-warning me-2"></i> Stories & Articles Management</h3>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Publish New Story</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Story Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. The Recovery of Gauri">
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Short Summary</label>
                    <textarea name="summary" rows="2" class="form-control" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label font-ui small fw-bold">Story Content (HTML/Text)</label>
                    <textarea name="content" rows="6" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">Publish Story</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Published Articles</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stories as $s): ?>
                            <tr>
                                <td class="font-mono"><?php echo e($s['published_at']); ?></td>
                                <td><strong><?php echo e($s['title']); ?></strong></td>
                                <td><?php echo e($s['author']); ?></td>
                                <td><span class="badge bg-success"><?php echo e($s['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
