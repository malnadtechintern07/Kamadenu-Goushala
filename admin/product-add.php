<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

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

    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';

    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, name_kn, name_hi, slug, description, price, stock_quantity, unit, is_active, image, whatsapp_number_id, contact_method, whatsapp_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $name, $name_kn, $name_hi, $slug, $description, $price, $stock, $unit, $image_path, $whatsapp_number_id, $contact_method, $whatsapp_message]);
    $product_id = $pdo->lastInsertId();

    // Create Inventory record
    $pdo->prepare("INSERT INTO inventory (product_id, current_stock, min_threshold, max_capacity) VALUES (?, ?, 10, 500)")->execute([$product_id, $stock]);

    log_audit($pdo, 'Add Product', 'products', $product_id);
    header("Location: /Kamadhenu-goushala/admin/products.php");
    exit;
}

require_once __DIR__ . '/header.php';
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY id ASC")->fetchAll();
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
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
            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" selected>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp">WhatsApp Contact (Direct Message)</option>
                                <option value="both">Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle text-warning"></i> <strong>Note:</strong> This local setting is overridden by the global <strong>Product Checkout Method</strong> option in <a href="/Kamadhenu-goushala/admin/settings.php" class="text-warning">System Configuration</a>.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Store Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>">
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" placeholder="e.g. Hare Krishna! I would like to buy product from store. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
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
        <a href="/Kamadhenu-goushala/admin/products.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
