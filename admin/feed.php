<?php
require_once __DIR__ . '/header.php';

// Fetch WhatsApp numbers for dropdown select options
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY label ASC")->fetchAll();

// Fetch sanctuary cows to allow selecting an existing cow
$sanctuary_cows = $pdo->query("SELECT id, cow_code, name, breed, photo, rescue_story, monthly_sponsorship_amount FROM cows ORDER BY name ASC")->fetchAll();

// Handle add feeding cow submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_feed_cow']) || isset($_POST['add_feed_item']))) {
    $cow_code = trim($_POST['cow_code']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $feed_amount = floatval($_POST['feed_amount'] ?? $_POST['cost'] ?? 500);
    $is_available = isset($_POST['is_available']) ? intval($_POST['is_available']) : 1;
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : (isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'both');
    $wa_id = !empty($_POST['whatsapp_number_id']) ? intval($_POST['whatsapp_number_id']) : null;
    $wa_msg = isset($_POST['whatsapp_message']) ? trim($_POST['whatsapp_message']) : '';

    // Handle photo upload
    $photo_path = 'assets/images/cow-default.jpg';
    $uploaded_photo = handle_file_upload('photo_file');
    if (empty($uploaded_photo)) {
        $uploaded_photo = handle_file_upload('image');
    }
    $url_photo = isset($_POST['photo_url']) ? trim($_POST['photo_url']) : '';

    if (!empty($uploaded_photo)) {
        $photo_path = $uploaded_photo;
    } elseif (!empty($url_photo)) {
        $photo_path = $url_photo;
    }

    $stmt = $pdo->prepare("INSERT INTO feeding_cows (cow_code, name, description, photo, feed_amount, is_available, payment_method, whatsapp_number_id, whatsapp_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cow_code, $name, $description, $photo_path, $feed_amount, $is_available, $payment_method, $wa_id, $wa_msg]);

    log_audit($pdo, 'Add Feeding Cow', 'feeding_cows', $pdo->lastInsertId());
    header("Location: /Kamadhenu-goushala/admin/feed.php?saved=1");
    exit;
}

// Fetch all registered feeding cows
$feeding_cows = $pdo->query("SELECT fc.*, wn.label as wa_label FROM feeding_cows fc LEFT JOIN whatsapp_numbers wn ON fc.whatsapp_number_id = wn.id ORDER BY fc.id DESC")->fetchAll();

// Fetch recent feeding contributions
$feeding_logs = $pdo->query("SELECT fl.*, fc.name as cow_name, fc.cow_code FROM feeding_cow_logs fl JOIN feeding_cows fc ON fl.feeding_cow_id = fc.id ORDER BY fl.id DESC LIMIT 15")->fetchAll();

// Map sanctuary cows as JSON for quick form autofill
$sanctuary_cows_map = [];
foreach ($sanctuary_cows as $sc) {
    $sanctuary_cows_map[$sc['id']] = [
        'cow_code' => 'FC-' . str_replace('KG-', '', $sc['cow_code']),
        'name' => $sc['name'],
        'description' => $sc['rescue_story'],
        'photo' => img_url($sc['photo']),
        'feed_amount' => 500.00
    ];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0 text-emerald" style="color: #10b981;"><i class="fas fa-cookie-bite text-success me-2"></i> Feed Cow Manager</h3>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success border-success bg-success bg-opacity-10 text-success">Feeding cow registered successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success border-success bg-success bg-opacity-10 text-success">Feeding cow details updated successfully.</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Add Feed Cow Form -->
    <div class="col-lg-5">
        <div class="kamadenu-card p-4 border-success border-opacity-25">
            <h4 class="font-heading mb-3 text-success"><i class="fas fa-plus-circle me-1"></i> Register Cow for Feeding</h4>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_feed_cow" value="1">
                
                <!-- Quick Autofill Dropdown from Sanctuary Cows -->
                <div class="mb-3 p-3 bg-dark border border-success border-opacity-50 rounded">
                    <label class="form-label font-ui small fw-bold text-success"><i class="fas fa-magic me-1"></i> Select Existing Sanctuary Cattle (Optional)</label>
                    <select id="sanctuary_cow_select" class="form-select font-ui" onchange="autofillSanctuaryCow(this.value)">
                        <option value="">-- Choose Existing Cattle to Pre-fill --</option>
                        <?php foreach ($sanctuary_cows as $sc): ?>
                            <option value="<?php echo $sc['id']; ?>">
                                <?php echo e($sc['cow_code']); ?> - <?php echo e($sc['name']); ?> (<?php echo e($sc['breed']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted fs-7 mt-1 d-block">Selecting a cattle will automatically populate its code, name, story, and photo URL.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Cow Unique Code (ID)</label>
                        <input type="text" name="cow_code" id="cow_code" class="form-control font-mono" placeholder="FC-101" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Cow Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Ganga" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Feed Contribution Amount (₹)</label>
                        <input type="number" step="0.01" name="feed_amount" id="feed_amount" class="form-control font-mono" value="500.00" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white">Availability Status</label>
                        <select name="is_available" class="form-select">
                            <option value="1" selected>Available (Public)</option>
                            <option value="0">Hidden / Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white"><i class="fas fa-upload text-success me-1"></i> Upload Photo File</label>
                        <input type="file" name="photo_file" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold text-white"><i class="fas fa-link text-success me-1"></i> Image URL (Paste)</label>
                        <input type="text" name="photo_url" id="photo_url" class="form-control font-mono" placeholder="https://...">
                    </div>

                    <!-- Payment Method Configuration -->
                    <div class="col-12 border-top border-secondary border-opacity-50 pt-3 mt-2">
                        <h5 class="font-heading small fw-bold text-success mb-2"><i class="fab fa-whatsapp me-1"></i> Payment &amp; Checkout Action Mode</h5>
                        
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold text-white">Allowed Payment Options for User</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="website">Website Payment Only</option>
                                <option value="whatsapp">WhatsApp Payment Only</option>
                                <option value="both" selected>Both (Show Website Payment & WhatsApp Buttons)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold text-white">WhatsApp Recipient Helpline</label>
                            <select name="whatsapp_number_id" class="form-select">
                                <option value="">Default Gouseva Support Number</option>
                                <?php foreach ($wa_numbers as $num): ?>
                                    <option value="<?php echo $num['id']; ?>"><?php echo e($num['label']); ?> (<?php echo e($num['phone_number']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold text-white">WhatsApp Pre-filled Customer Message</label>
                            <textarea name="whatsapp_message" rows="2" class="form-control" placeholder="e.g. Hare Krishna! I want to feed this cow..."></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-ui small fw-bold text-white">Description / Story</label>
                        <textarea name="description" id="description" rows="3" class="form-control" required placeholder="Describe the cow or feeding details..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 font-ui fw-bold mt-4 py-2.5 shadow" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">Save &amp; Publish Feeding Cow</button>
            </form>
        </div>
    </div>

    <!-- Right Column: Feeding Cows Table & Recent Logs -->
    <div class="col-lg-7">
        <!-- Feeding Cows Catalog List -->
        <div class="kamadenu-card p-4 mb-4 border-success border-opacity-25">
            <h4 class="font-heading mb-3 text-success"><i class="fas fa-list-check me-1"></i> Cows Available for Feeding</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white-50">
                    <thead>
                        <tr class="text-white border-bottom border-success border-opacity-50">
                            <th>Preview</th>
                            <th>Code &amp; Name</th>
                            <th>Feed Cost</th>
                            <th>Payment Option</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feeding_cows)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No cows registered for feeding yet. Use the form to add one.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($feeding_cows as $c): ?>
                                <tr class="border-bottom border-secondary border-opacity-25">
                                    <td>
                                        <img src="<?php echo img_url($c['photo']); ?>" class="rounded shadow-sm border border-success border-opacity-50" style="width: 48px; height: 48px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                                    </td>
                                    <td>
                                        <span class="badge-cow-code me-1" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);"><?php echo e($c['cow_code']); ?></span>
                                        <strong class="text-white"><?php echo e($c['name']); ?></strong>
                                    </td>
                                    <td class="font-mono fw-bold text-success">₹<?php echo number_format($c['feed_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 text-capitalize"><?php echo e($c['payment_method']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($c['is_available']): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/Kamadhenu-goushala/admin/feed-edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-success font-ui fw-bold"><i class="fas fa-edit"></i> Edit</a>
                                        <button onclick="deleteAdminItem('feeding_cows', <?php echo $c['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="kamadenu-card p-4 border-success border-opacity-25">
            <h4 class="font-heading mb-3 text-success"><i class="fas fa-history me-1"></i> Recent Feed Contributions</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white-50">
                    <thead>
                        <tr class="text-white border-bottom border-success border-opacity-50">
                            <th>ID</th>
                            <th>Cattle</th>
                            <th>Sponsor Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feeding_logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No feeding logs recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($feeding_logs as $log): ?>
                                <tr class="border-bottom border-secondary border-opacity-25">
                                    <td class="font-mono small text-success">#FL-<?php echo $log['id']; ?></td>
                                    <td>
                                        <strong class="text-white"><?php echo e($log['cow_name']); ?></strong>
                                        <small class="text-white-50 font-mono d-block"><?php echo e($log['cow_code']); ?></small>
                                    </td>
                                    <td><?php echo e($log['sponsor_name']); ?></td>
                                    <td class="font-mono text-success fw-bold">₹<?php echo number_format($log['amount_paid'], 2); ?></td>
                                    <td class="font-mono small"><?php echo e($log['date_sponsored']); ?></td>
                                    <td><span class="badge bg-success"><?php echo e($log['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const sanctuaryCowsMap = <?php echo json_encode($sanctuary_cows_map); ?>;

function autofillSanctuaryCow(scId) {
    if (!scId || !sanctuaryCowsMap[scId]) return;
    const item = sanctuaryCowsMap[scId];
    
    document.getElementById('cow_code').value = item.cow_code;
    document.getElementById('name').value = item.name;
    document.getElementById('description').value = item.description;
    document.getElementById('photo_url').value = item.photo;
    document.getElementById('feed_amount').value = item.feed_amount;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
