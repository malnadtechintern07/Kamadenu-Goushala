<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
$stmt->execute([$id]);
$seva = $stmt->fetch();

if (!$seva) {
    header("Location: /Kamadenu/admin/seva.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $title_kn = trim($_POST['title_kn']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $amount = floatval($_POST['suggested_amount']);
    $icon = trim($_POST['icon']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE seva SET title = ?, title_kn = ?, description = ?, category = ?, suggested_amount = ?, icon = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$title, $title_kn, $description, $category, $amount, $icon, $is_active, $id]);

    log_audit($pdo, 'Edit Seva Item', 'seva', $id);
    header("Location: /Kamadenu/admin/seva.php?updated=1");
    exit;
}
?>

<h3 class="font-heading mb-4"><i class="fas fa-edit text-warning me-2"></i> Edit Seva Activity</h3>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Seva Title (English)</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($seva['title']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Seva Title (Kannada)</label>
                <input type="text" name="title_kn" class="form-control kn-text" value="<?php echo e($seva['title_kn']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Category</label>
                <input type="text" name="category" class="form-control" value="<?php echo e($seva['category']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Suggested Amount (INR)</label>
                <input type="number" step="0.01" name="suggested_amount" class="form-control font-mono" value="<?php echo $seva['suggested_amount']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">FontAwesome Icon Class</label>
                <input type="text" name="icon" class="form-control font-mono" value="<?php echo e($seva['icon']); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Description</label>
                <textarea name="description" rows="3" class="form-control" required><?php echo e($seva['description']); ?></textarea>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" <?php echo $seva['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label font-ui fw-bold" for="activeCheck">Active in Public Seva Catalog</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Update Seva Activity in MySQL</button>
        <a href="/Kamadenu/admin/seva.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
