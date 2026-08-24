<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: /Kamadenu/admin/products.php");
    exit;
}

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

    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';

    $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, name_kn = ?, name_hi = ?, description = ?, price = ?, stock_quantity = ?, unit = ?, is_active = ?, image = ?, whatsapp_number_id = ?, contact_method = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$category_id, $name, $name_kn, $name_hi, $description, $price, $stock, $unit, $is_active, $image_path, $whatsapp_number_id, $contact_method, $whatsapp_message, $id]);

    $pdo->prepare("UPDATE inventory SET current_stock = ? WHERE product_id = ?")->execute([$stock, $id]);

    log_audit($pdo, 'Edit Product', 'products', $id);
    header("Location: /Kamadenu/admin/products.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY id ASC")->fetchAll();
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
            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" <?php echo $product['contact_method'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $product['contact_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Contact (Direct Message)</option>
                                <option value="both" <?php echo $product['contact_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle text-warning"></i> <strong>Note:</strong> This local setting is overridden by the global <strong>Product Checkout Method</strong> option in <a href="/Kamadenu/admin/settings.php" class="text-warning">System Configuration</a>.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Store Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($product['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($product['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I would like to buy product from store. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
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
