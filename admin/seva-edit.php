<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
$stmt->execute([$id]);
$seva = $stmt->fetch();

if (!$seva) {
    header("Location: /Kamadhenu-goushala/admin/seva.php");
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
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;

    $stmt = $pdo->prepare("UPDATE seva SET title = ?, title_kn = ?, description = ?, category = ?, suggested_amount = ?, icon = ?, is_active = ?, whatsapp_number_id = ?, contact_method = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$title, $title_kn, $description, $category, $amount, $icon, $is_active, $whatsapp_number_id, $contact_method, $whatsapp_message, $id]);

    log_audit($pdo, 'Edit Seva Item', 'seva', $id);
    header("Location: /Kamadhenu-goushala/admin/seva.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
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
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" <?php echo $seva['contact_method'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $seva['contact_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Contact (Direct Message)</option>
                                <option value="both" <?php echo $seva['contact_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted">Choose how user bookings are processed for this seva.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Order Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($seva['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Store WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($seva['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I want to sponsor this seva. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
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
        <a href="/Kamadhenu-goushala/admin/seva.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
