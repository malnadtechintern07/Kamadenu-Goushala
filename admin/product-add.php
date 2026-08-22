<?php
require_once __DIR__ . '/header.php';

$categories = $pdo->query("SELECT * FROM product_categories ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $name_kn = trim($_POST['name_kn']);
    $name_hi = trim($_POST['name_hi']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $unit = trim($_POST['unit']);
    $description = trim($_POST['description']);

    // Handle Photo Upload / URL Input
    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $image_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $image_path = $url_photo;
    } else {
        $image_path = 'assets/images/product-default.jpg';
    }

    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, name_kn, name_hi, slug, description, price, stock_quantity, unit, is_active, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
    $stmt->execute([$category_id, $name, $name_kn, $name_hi, $slug, $description, $price, $stock, $unit, $image_path]);
    $product_id = $pdo->lastInsertId();

    // Create Inventory record
    $pdo->prepare("INSERT INTO inventory (product_id, current_stock, min_threshold, max_capacity) VALUES (?, ?, 10, 500)")->execute([$product_id, $stock]);

    log_audit($pdo, 'Add Product', 'products', $product_id);
    header("Location: /Kamadenu/admin/products.php");
    exit;
}
?>

<h3 class="font-heading mb-4"><i class="fas fa-plus-circle text-warning me-2"></i> Add New Product to Store</h3>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (English)</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. A2 Vedic Gir Cow Ghee (500ml)" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (Kannada)</label>
                <input type="text" name="name_kn" class="form-control kn-text" placeholder="ಉದಾ. A2 ತುಪ್ಪ">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Product Name (Hindi)</label>
                <input type="text" name="name_hi" class="form-control" placeholder="उदा. A2 गिर गाय घी">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Price (INR)</label>
                <input type="number" step="0.01" name="price" class="form-control font-mono" placeholder="1250.00" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Initial Stock Quantity</label>
                <input type="number" name="stock_quantity" class="form-control font-mono" value="50" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="bottle">bottle</option>
                    <option value="pack">pack</option>
                    <option value="kg">kg</option>
                    <option value="ltr">ltr</option>
                </select>
            </div>

            <!-- Product Photo Inputs -->
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload Product Image File</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" placeholder="assets/images/product-default.jpg">
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Product Description</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Describe product benefits, preparation method..." required></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Add Product to Store Catalog</button>
        <a href="/Kamadenu/admin/products.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
