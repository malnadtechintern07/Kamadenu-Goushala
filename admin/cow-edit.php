<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
$stmt->execute([$id]);
$cow = $stmt->fetch();

if (!$cow) {
    require_once __DIR__ . '/header.php';
    echo "<div class='container mt-5'><div class='alert alert-danger'>Cow not found.</div></div>";
    require_once __DIR__ . '/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $name_kn = trim($_POST['name_kn']);
    $breed = trim($_POST['breed']);
    $age_years = intval($_POST['age_years']);
    $weight_kg = floatval($_POST['weight_kg']);
    $monthly_amount = floatval($_POST['monthly_amount']);
    $health_status = $_POST['health_status'];
    $adoption_status = $_POST['adoption_status'];
    $rescue_story = trim($_POST['rescue_story']);
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';

    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = trim($_POST['photo_url']);

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    } else {
        $photo_path = $cow['photo'];
    }

    $stmt = $pdo->prepare("UPDATE cows SET name = ?, name_kn = ?, breed = ?, age_years = ?, weight_kg = ?, monthly_sponsorship_amount = ?, health_status = ?, adoption_status = ?, rescue_story = ?, photo = ?, whatsapp_number_id = ?, contact_method = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$name, $name_kn, $breed, $age_years, $weight_kg, $monthly_amount, $health_status, $adoption_status, $rescue_story, $photo_path, $whatsapp_number_id, $contact_method, $whatsapp_message, $id]);

    log_audit($pdo, 'Edit Cow', 'cows', $id);
    header("Location: /Kamadhenu-goushala/admin/cows.php?updated=1");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Cattle Profile (<?php echo e($cow['cow_code']); ?>)</h3>
    <a href="/Kamadhenu-goushala/admin/cows.php" class="btn btn-outline-secondary font-ui">&larr; Back to Cows</a>
</div>

<div class="kamadenu-card p-4">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Cow Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($cow['name']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Name (Kannada)</label>
                <input type="text" name="name_kn" class="form-control kn-text" value="<?php echo e($cow['name_kn']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Breed</label>
                <input type="text" name="breed" class="form-control" value="<?php echo e($cow['breed']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Age (Years)</label>
                <input type="number" name="age_years" class="form-control font-mono" value="<?php echo $cow['age_years']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Weight (kg)</label>
                <input type="number" step="0.1" name="weight_kg" class="form-control font-mono" value="<?php echo $cow['weight_kg']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Monthly Care Cost (INR)</label>
                <input type="number" step="0.01" name="monthly_amount" class="form-control font-mono" value="<?php echo $cow['monthly_sponsorship_amount']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Health Status</label>
                <select name="health_status" class="form-select">
                    <option value="Excellent" <?php echo $cow['health_status'] === 'Excellent' ? 'selected' : ''; ?>>Excellent</option>
                    <option value="Good" <?php echo $cow['health_status'] === 'Good' ? 'selected' : ''; ?>>Good</option>
                    <option value="Under Treatment" <?php echo $cow['health_status'] === 'Under Treatment' ? 'selected' : ''; ?>>Under Treatment</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Adoption Status</label>
                <select name="adoption_status" class="form-select">
                    <option value="Available" <?php echo $cow['adoption_status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                    <option value="Sponsored" <?php echo $cow['adoption_status'] === 'Sponsored' ? 'selected' : ''; ?>>Sponsored</option>
                </select>
            </div>
            <div class="col-12">
                <div class="p-3 bg-light border border-warning border-opacity-25 rounded-3 mb-2">
                    <h5 class="text-warning font-heading small fw-bold mb-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp & Checkout Action Integration</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">Checkout Action Mode</label>
                            <select name="contact_method" class="form-select">
                                <option value="website" <?php echo $cow['contact_method'] === 'website' ? 'selected' : ''; ?>>Website Checkout (Standard Gateway)</option>
                                <option value="whatsapp" <?php echo $cow['contact_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Contact (Direct Message)</option>
                                <option value="both" <?php echo $cow['contact_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website & WhatsApp Options to User)</option>
                            </select>
                            <small class="text-muted">Choose how user adoptions are processed for this cow.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Adoption Number --</option>
                                <?php foreach ($wa_numbers as $wn): ?>
                                    <option value="<?php echo $wn['id']; ?>" <?php echo intval($cow['whatsapp_number_id']) === intval($wn['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($wn['label']); ?> (<?php echo e($wn['phone_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Defaults to global Gouseva WhatsApp number if none selected.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-ui small fw-bold">WhatsApp Pre-filled Customer Message (Optional)</label>
                            <input type="text" name="whatsapp_message" class="form-control" value="<?php echo e($cow['whatsapp_message']); ?>" placeholder="e.g. Hare Krishna! I want to adopt this cow. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Cow Photo Preview -->
            <div class="col-12">
                <div class="p-3 bg-light border rounded d-flex align-items-center gap-3">
                    <img src="<?php echo img_url($cow['photo']); ?>" class="rounded shadow-sm" style="width: 100px; height: 80px; object-fit: cover; flex-shrink: 0;">
                    <div>
                        <strong class="d-block font-heading">Current Active Cow Photo</strong>
                        <small class="text-muted font-mono d-block"><?php echo e($cow['photo']); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-upload text-warning me-1"></i> Option 1: Upload New Cow Photo File</label>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold text-dark"><i class="fas fa-link text-warning me-1"></i> Option 2: Enter / Paste Image URL</label>
                <input type="text" name="photo_url" class="form-control font-mono" value="<?php echo e($cow['photo']); ?>">
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Rescue Story Chronicle</label>
                <textarea name="rescue_story" rows="4" class="form-control" required><?php echo e($cow['rescue_story']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2 text-white">
            <i class="fas fa-save me-1"></i> Save Passport & Update Photo in MySQL
        </button>
        <a href="/Kamadhenu-goushala/admin/cows.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
