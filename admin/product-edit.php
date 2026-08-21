<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: /Kamadenu/admin/products.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM product_categories ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $name_kn = trim($_POST['name_kn']);
    $name_hi = trim($_POST['name_hi']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $unit = trim($_POST['unit']);
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $image_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $image_path = $url_photo;
    } else {
        $image_path = $product['image'];
    }

    $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, name_kn = ?, name_hi = ?, description = ?, price = ?, stock_quantity = ?, unit = ?, is_active = ?, image = ? WHERE id = ?");
    $stmt->execute([$category_id, $name, $name_kn, $name_hi, $description, $price, $stock, $unit, $is_active, $image_path, $id]);

    $pdo->prepare("UPDATE inventory SET current_stock = ? WHERE product_id = ?")->execute([$stock, $id]);

    log_audit($pdo, 'Edit Product', 'products', $id);
    header("Location: /Kamadenu/admin/products.php?updated=1");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Product & Update Photo</h3>
    <a href="/Kamadenu/admin/products.php" class="btn btn-outline-secondary font-ui">&larr; Back to Products</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] === $cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (English)</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($product['name']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (Kannada)</label>
                <input type="text" name="name_kn" class="form-control kn-text" value="<?php echo e($product['name_kn']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (Hindi)</label>
                <input type="text" name="name_hi" class="form-control" value="<?php echo e($product['name_hi']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Price (INR)</label>
                <input type="number" step="0.01" name="price" class="form-control font-mono" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Stock Quantity</label>
                <input type="number" name="stock_quantity" class="form-control font-mono" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Unit</label>
                <input type="text" name="unit" class="form-control" value="<?php echo e($product['unit']); ?>" required>
            </div>

            <!-- Current Product Image Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                    <img src="<?php echo img_url($product['image']); ?>" width="90" height="80" class="rounded object-fit-cover shadow-sm">
                    <div>
                        <strong class="d-block font-heading">Current Active Product Image</strong>
                        <small class="text-muted font-mono d-block"><?php echo e($product['image']); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload New Product Image File</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" value="<?php echo e($product['image']); ?>">
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Description</label>
                <textarea name="description" rows="4" class="form-control" required><?php echo e($product['description']); ?></textarea>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label font-ui fw-bold" for="activeCheck">Active in Store Catalog</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2 text-white">
            <i class="fas fa-save me-1"></i> Update Product & Image in MySQL
        </button>
        <a href="/Kamadenu/admin/products.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
