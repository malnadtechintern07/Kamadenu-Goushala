<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM feeding_cows WHERE id = ?");
$stmt->execute([$id]);
$cow = $stmt->fetch();

if (!$cow) {
    header("Location: /Kamadhenu-goushala/admin/feed.php");
    exit;
}

$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY label ASC")->fetchAll();
$sanctuary_cows = $pdo->query("SELECT id, cow_code, name, breed, photo, rescue_story, monthly_sponsorship_amount FROM cows ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $feed_amount = floatval($_POST['feed_amount']);
    $is_available = isset($_POST['is_available']) ? intval($_POST['is_available']) : 1;
    $payment_method = trim($_POST['payment_method']);
    $wa_id = !empty($_POST['whatsapp_number_id']) ? intval($_POST['whatsapp_number_id']) : null;
    $wa_msg = isset($_POST['whatsapp_message']) ? trim($_POST['whatsapp_message']) : '';

    // Handle photo upload
    $uploaded_photo = handle_file_upload('photo_file');
    $url_photo = isset($_POST['photo_url']) ? trim($_POST['photo_url']) : '';

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    } else {
        $photo_path = $cow['photo'];
    }

    $stmt = $pdo->prepare("UPDATE feeding_cows SET name = ?, description = ?, photo = ?, feed_amount = ?, is_available = ?, payment_method = ?, whatsapp_number_id = ?, whatsapp_message = ? WHERE id = ?");
    $stmt->execute([$name, $description, $photo_path, $feed_amount, $is_available, $payment_method, $wa_id, $wa_msg, $id]);

    log_audit($pdo, 'Edit Feeding Cow', 'feeding_cows', $id);
    header("Location: /Kamadhenu-goushala/admin/feed.php?updated=1");
    exit;
}

// Map sanctuary cows as JSON for quick form autofill
$sanctuary_cows_map = [];
foreach ($sanctuary_cows as $sc) {
    $sanctuary_cows_map[$sc['id']] = [
        'name' => $sc['name'],
        'description' => $sc['rescue_story'],
        'photo' => img_url($sc['photo']),
        'feed_amount' => 500.00
    ];
}

require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0 text-success" style="color: #10b981;"><i class="fas fa-edit text-success me-2"></i> Edit Feeding Cow (<?php echo e($cow['cow_code']); ?>)</h3>
    <a href="/Kamadhenu-goushala/admin/feed.php" class="btn btn-outline-success font-ui fw-bold">&larr; Back to Manager</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kamadenu-card p-5 border-success border-opacity-25">
            <form method="POST" enctype="multipart/form-data">
                
                <!-- Quick Autofill Dropdown from Sanctuary Cows -->
                <div class="mb-4 p-3 bg-dark border border-success border-opacity-50 rounded">
                    <label class="form-label font-ui small fw-bold text-success"><i class="fas fa-magic me-1"></i> Pre-fill details from Sanctuary Cattle (Optional)</label>
                    <select id="sanctuary_cow_select" class="form-select font-ui" onchange="autofillSanctuaryCow(this.value)">
                        <option value="">-- Choose Cattle to Overwrite Details --</option>
                        <?php foreach ($sanctuary_cows as $sc): ?>
                            <option value="<?php echo $sc['id']; ?>">
                                <?php echo e($sc['cow_code']); ?> - <?php echo e($sc['name']); ?> (<?php echo e($sc['breed']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Cow Unique Code (ID)</label>
                        <input type="text" class="form-control font-mono" value="<?php echo e($cow['cow_code']); ?>" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Cow Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo e($cow['name']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Suggested Feed Amount (₹)</label>
                        <input type="number" step="0.01" name="feed_amount" id="feed_amount" class="form-control font-mono" value="<?php echo $cow['feed_amount']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Availability Status</label>
                        <select name="is_available" class="form-select">
                            <option value="1" <?php echo $cow['is_available'] == 1 ? 'selected' : ''; ?>>Available (Public)</option>
                            <option value="0" <?php echo $cow['is_available'] == 0 ? 'selected' : ''; ?>>Hidden / Inactive</option>
                        </select>
                    </div>

                    <!-- Current Cow Photo Preview -->
                    <div class="col-12">
                        <div class="p-3 bg-light border border-secondary border-opacity-25 rounded d-flex align-items-center gap-3">
                            <img id="photo_preview" src="<?php echo img_url($cow['photo']); ?>" class="rounded shadow-sm border border-success border-opacity-50" style="width: 100px; height: 80px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                            <div>
                                <strong class="d-block font-heading text-dark">Current Active Cow Photo</strong>
                                <small class="text-muted font-mono d-block"><?php echo e($cow['photo']); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white"><i class="fas fa-upload text-success me-1"></i> Option 1: Upload New Photo File</label>
                        <input type="file" name="photo_file" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white"><i class="fas fa-link text-success me-1"></i> Option 2: Enter / Paste Image URL</label>
                        <input type="text" name="photo_url" id="photo_url" class="form-control font-mono" value="<?php echo e($cow['photo']); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label font-ui small fw-bold text-white">Description / Story</label>
                        <textarea name="description" id="description" rows="4" class="form-control" required><?php echo e($cow['description']); ?></textarea>
                    </div>

                    <!-- Payment Method Configuration -->
                    <h5 class="font-heading border-top border-secondary border-opacity-50 pt-3 mt-4 mb-2 text-success col-12"><i class="fab fa-whatsapp me-1"></i> Payment &amp; Checkout Action Mode</h5>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Allowed Payment Options for User</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="website" <?php echo $cow['payment_method'] === 'website' ? 'selected' : ''; ?>>Website Gateway Payment Only</option>
                            <option value="whatsapp" <?php echo $cow['payment_method'] === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp Payment Only</option>
                            <option value="both" <?php echo $cow['payment_method'] === 'both' ? 'selected' : ''; ?>>Both (Show Website Payment & WhatsApp Buttons)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">WhatsApp Recipient Helpline</label>
                        <select name="whatsapp_number_id" class="form-select font-mono">
                            <option value="">Default Gouseva Support Number</option>
                            <?php foreach ($wa_numbers as $num): ?>
                                <option value="<?php echo $num['id']; ?>" <?php echo intval($cow['whatsapp_number_id']) === intval($num['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($num['label']); ?> (<?php echo e($num['phone_number']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label font-ui small fw-bold text-white">WhatsApp Pre-filled Customer Message</label>
                        <textarea name="whatsapp_message" rows="3" class="form-control" placeholder="e.g. Hare Krishna! I want to feed this cow..."><?php echo e($cow['whatsapp_message']); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 font-ui fw-bold fs-5 shadow mt-3 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">Save Changes to Database</button>
            </form>
        </div>
    </div>
</div>

<script>
const sanctuaryCowsMap = <?php echo json_encode($sanctuary_cows_map); ?>;

function autofillSanctuaryCow(scId) {
    if (!scId || !sanctuaryCowsMap[scId]) return;
    const item = sanctuaryCowsMap[scId];
    
    document.getElementById('name').value = item.name;
    document.getElementById('description').value = item.description;
    document.getElementById('photo_url').value = item.photo;
    document.getElementById('photo_preview').src = item.photo;
    document.getElementById('feed_amount').value = item.feed_amount;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
