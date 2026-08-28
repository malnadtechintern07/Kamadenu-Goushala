<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_wa_number'])) {
        $label = trim($_POST['wa_label']);
        $phone = trim($_POST['wa_phone']);
        if (!empty($label) && !empty($phone)) {
            $stmt = $pdo->prepare("INSERT INTO whatsapp_numbers (label, phone_number) VALUES (?, ?)");
            $stmt->execute([$label, $phone]);
            log_audit($pdo, 'Add WhatsApp Number to Directory', 'whatsapp_numbers');
            header("Location: /Kamadhenu-goushala/admin/settings.php?saved=2");
            exit;
        }
    } elseif (isset($_POST['delete_wa_number'])) {
        $id = intval($_POST['wa_number_id']);
        $stmt = $pdo->prepare("DELETE FROM whatsapp_numbers WHERE id = ?");
        $stmt->execute([$id]);
        log_audit($pdo, 'Delete WhatsApp Number from Directory', 'whatsapp_numbers', $id);
        header("Location: /Kamadhenu-goushala/admin/settings.php?saved=3");
        exit;
    } else {
        foreach ($_POST['settings'] as $key => $val) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([trim($val), $key]);
        }
        if (isset($_FILES['donation_qr_code_file']) && $_FILES['donation_qr_code_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded_path = handle_file_upload('donation_qr_code_file');
            if (!empty($uploaded_path)) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'donation_qr_code'");
                $stmt->execute([$uploaded_path]);
            }
        }
        log_audit($pdo, 'Update System Settings', 'settings');
        header("Location: /Kamadhenu-goushala/admin/settings.php?saved=1");
        exit;
    }
}

require_once __DIR__ . '/header.php';

$settings = $pdo->query("SELECT * FROM settings ORDER BY id ASC")->fetchAll();
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();

// Group settings by their category group
$settings_by_group = [];
foreach ($settings as $s) {
    $settings_by_group[$s['setting_group']][] = $s;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-cog text-warning me-2"></i> System Configuration Control Panel</h3>
</div>

<?php if (isset($_GET['saved'])): ?>
    <?php if ($_GET['saved'] == '1'): ?>
        <div class="alert alert-success shadow-sm border-success">System settings saved successfully to MySQL.</div>
    <?php elseif ($_GET['saved'] == '2'): ?>
        <div class="alert alert-success shadow-sm border-success">New WhatsApp number successfully added to directory.</div>
    <?php elseif ($_GET['saved'] == '3'): ?>
        <div class="alert alert-danger shadow-sm border-danger">WhatsApp number successfully removed from directory.</div>
    <?php endif; ?>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <?php foreach ($settings_by_group as $group => $items): ?>
        <div class="kamadenu-card p-4 mb-4" id="settings-group-<?php echo e($group); ?>">
            <h4 class="font-heading border-bottom pb-2 mb-3 text-warning">
                <?php
                if ($group === 'general') {
                    echo '<i class="fas fa-sliders-h me-2"></i> General System Configuration';
                } elseif ($group === 'contact') {
                    echo '<i class="fas fa-address-book me-2"></i> Goushala Contact Information';
                } elseif ($group === 'payment') {
                    echo '<i class="fas fa-credit-card me-2"></i> Donations & Payment Gateway Configuration';
                } elseif ($group === 'whatsapp') {
                    echo '<i class="fab fa-whatsapp me-2"></i> WhatsApp Settings & Defaults';
                } else {
                    echo e(ucwords($group)) . ' Settings';
                }
                ?>
            </h4>
            <div class="row g-3">
                <?php foreach ($items as $s): ?>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold"><?php echo e(ucwords(str_replace('_', ' ', $s['setting_key']))); ?></label>
                        <?php if ($s['setting_key'] === 'donation_action_mode'): ?>
                            <select name="settings[<?php echo e($s['setting_key']); ?>]" class="form-select font-mono">
                                <option value="website" <?php echo $s['setting_value'] === 'website' ? 'selected' : ''; ?>>Website Payment Only</option>
                                <option value="whatsapp" <?php echo $s['setting_value'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Payment Only</option>
                                <option value="qrcode" <?php echo $s['setting_value'] === 'qrcode' ? 'selected' : ''; ?>>QR Code Payment Only</option>
                                <option value="both" <?php echo $s['setting_value'] === 'both' ? 'selected' : ''; ?>>Website & WhatsApp Payments</option>
                                <option value="website_qrcode" <?php echo $s['setting_value'] === 'website_qrcode' ? 'selected' : ''; ?>>Website & QR Code Payments</option>
                                <option value="whatsapp_qrcode" <?php echo $s['setting_value'] === 'whatsapp_qrcode' ? 'selected' : ''; ?>>WhatsApp & QR Code Payments</option>
                                <option value="all" <?php echo $s['setting_value'] === 'all' ? 'selected' : ''; ?>>All Options (Website, WhatsApp & QR Code)</option>
                            </select>
                        <?php elseif ($s['setting_key'] === 'product_checkout_method'): ?>
                            <select name="settings[<?php echo e($s['setting_key']); ?>]" class="form-select font-mono">
                                <option value="website" <?php echo $s['setting_value'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $s['setting_value'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Checkout (Direct Message)</option>
                                <option value="both" <?php echo $s['setting_value'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                        <?php elseif ($s['setting_key'] === 'donation_qr_code'): ?>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars(img_url($s['setting_value'])); ?>" alt="Donation QR Code" class="img-thumbnail" style="max-height: 60px; width: auto; border-radius: 8px;">
                                    <div class="flex-grow-1">
                                        <input type="text" name="settings[<?php echo e($s['setting_key']); ?>]" class="form-control font-mono bg-dark text-white-50" value="<?php echo e($s['setting_value']); ?>" readonly>
                                    </div>
                                </div>
                                <input type="file" name="donation_qr_code_file" class="form-control">
                            </div>
                        <?php else: ?>
                            <input type="text" name="settings[<?php echo e($s['setting_key']); ?>]" class="form-control font-mono" value="<?php echo e($s['setting_value']); ?>">
                        <?php endif; ?>
                        <small class="text-muted d-block mt-1"><?php echo e($s['description']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="mt-4 mb-4">
        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-5 py-3 shadow">
            <i class="fas fa-save me-2"></i> Save All Settings
        </button>
    </div>
</form>

<!-- WhatsApp Numbers Directory Card -->
<div class="kamadenu-card p-4 mb-5">
    <h4 class="font-heading border-bottom pb-2 mb-3 text-warning">
        <i class="fab fa-whatsapp me-2"></i> WhatsApp Numbers Directory
    </h4>
    
    <!-- List of Existing Numbers -->
    <div class="table-responsive mb-4">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th class="font-ui small fw-bold">Label / Department</th>
                    <th class="font-ui small fw-bold">WhatsApp Phone Number</th>
                    <th class="font-ui small fw-bold text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($wa_numbers)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted font-ui py-3">No custom WhatsApp numbers added yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($wa_numbers as $wn): ?>
                        <tr>
                            <td class="fw-bold font-ui"><?php echo e($wn['label']); ?></td>
                            <td class="font-mono text-warning"><?php echo e($wn['phone_number']); ?></td>
                            <td class="text-end">
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this number?');" style="display:inline-block;">
                                    <input type="hidden" name="wa_number_id" value="<?php echo $wn['id']; ?>">
                                    <button type="submit" name="delete_wa_number" class="btn btn-outline-danger btn-sm rounded-pill font-ui">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Form to Add New Number -->
    <form method="POST" class="bg-black bg-opacity-25 p-3 rounded border border-warning border-opacity-10">
        <h5 class="font-heading fs-6 mb-3 text-white"><i class="fas fa-plus-circle me-1 text-warning"></i> Add New WhatsApp Contact</h5>
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label font-ui small fw-bold text-white-50">Label / Department Name</label>
                <input type="text" name="wa_label" class="form-control" placeholder="e.g. Donation Helpdesk" required>
            </div>
            <div class="col-md-5">
                <label class="form-label font-ui small fw-bold text-white-50">WhatsApp Phone Number (with Country Code)</label>
                <input type="text" name="wa_phone" class="form-control font-mono" placeholder="e.g. +91 98800 12345" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="add_wa_number" class="btn btn-warning w-100 font-ui fw-bold py-2">
                    <i class="fas fa-plus me-1"></i> Add
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
