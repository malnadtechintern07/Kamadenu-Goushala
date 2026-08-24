<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $story = trim($_POST['story']);
    $target_amount = floatval($_POST['target_amount']);
    $urgency_level = $_POST['urgency_level'];
    $start_date = date('Y-m-d');
    $whatsapp_number_id = isset($_POST['whatsapp_number_id']) && trim($_POST['whatsapp_number_id']) !== '' ? intval($_POST['whatsapp_number_id']) : NULL;
    $contact_method = isset($_POST['contact_method']) ? trim($_POST['contact_method']) : 'website';
    $whatsapp_message = isset($_POST['whatsapp_message']) && trim($_POST['whatsapp_message']) !== '' ? trim($_POST['whatsapp_message']) : NULL;

    $stmt = $pdo->prepare("INSERT INTO emergency_campaigns (title, story, target_amount, raised_amount, status, urgency_level, start_date, whatsapp_number_id, contact_method, whatsapp_message) VALUES (?, ?, ?, 0.00, 'Active', ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $story, $target_amount, $urgency_level, $start_date, $whatsapp_number_id, $contact_method, $whatsapp_message]);

    log_audit($pdo, 'Create Emergency Campaign', 'emergency_campaigns', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/emergency.php");
    exit;
}

require_once __DIR__ . '/header.php';
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<h3 class="font-heading mb-4"><i class="fas fa-plus-circle text-danger me-2"></i> Create Emergency Relief Campaign</h3>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label font-ui small fw-bold">Campaign Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Urgent Rescue Fodder Relief" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Target Funding Goal (INR)</label>
                <input type="number" name="target_amount" class="form-control font-mono" placeholder="100000" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Urgency Level</label>
                <select name="urgency_level" class="form-select">
                    <option value="Critical">Critical</option>
                    <option value="High">High</option>
                    <option value="Normal">Normal</option>
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
                            <small class="text-muted">Choose how user payments are processed for this campaign.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-ui small fw-bold">WhatsApp Contact Phone (Optional)</label>
                            <select name="whatsapp_number_id" class="form-select font-mono">
                                <option value="">-- Use Default Order Number --</option>
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
                            <input type="text" name="whatsapp_message" class="form-control" placeholder="e.g. Hare Krishna! I want to donate to the emergency rescue campaign. Please guide me.">
                            <small class="text-muted">Pre-populated text inside the user's WhatsApp message box when initiating chat.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Rescue Story & Emergency Details</label>
                <textarea name="story" rows="4" class="form-control" required placeholder="Explain why urgent relief is needed..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-danger font-ui fw-bold px-4 py-2">Publish Emergency Campaign</button>
        <a href="/Kamadenu/admin/emergency.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
