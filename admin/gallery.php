<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $image = trim($_POST['image']);
    $caption = trim($_POST['caption']);

    $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image, caption) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $category, $image, $caption]);

    log_audit($pdo, 'Add Gallery Image', 'gallery', $pdo->lastInsertId());
    header("Location: /Kamadhenu-goushala/admin/gallery.php?saved=1");
    exit;
}

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-images text-warning me-2"></i> Photo Gallery Management</h3>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Add Photo</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Category</label>
                    <select name="category" class="form-select">
                        <option value="Goushala Life">Goushala Life</option>
                        <option value="Seva">Seva</option>
                        <option value="Events">Events</option>
                        <option value="Rescue">Rescue</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Image URL</label>
                    <input type="text" name="image" class="form-control" placeholder="https://..." required>
                </div>
                <div class="mb-4">
                    <label class="form-label font-ui small fw-bold">Caption</label>
                    <input type="text" name="caption" class="form-control">
                </div>
                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">Add to Gallery</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3">
            <?php foreach ($gallery as $g): ?>
                <div class="col-md-6">
                    <div class="kamadenu-card p-3 d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?php echo e($g['image']); ?>" class="rounded" style="width: 70px; height: 70px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                            <div>
                                <span class="badge bg-secondary-subtle text-dark font-ui mb-1"><?php echo e($g['category']); ?></span>
                                <h5 class="font-heading mb-0 fs-6"><?php echo e($g['title']); ?></h5>
                                <small class="text-muted d-block"><?php echo e($g['caption']); ?></small>
                            </div>
                        </div>
                        <button onclick="deleteAdminItem('gallery', <?php echo $g['id']; ?>)" class="btn btn-sm btn-outline-danger p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete Photo"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
