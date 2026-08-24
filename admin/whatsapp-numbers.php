<?php
require_once __DIR__ . '/header.php';

// Handle Add/Edit Form Actions
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_wn = null;
if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM whatsapp_numbers WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_wn = $stmt->fetch();
}

// Processing Save / Add / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $label = trim($_POST['label']);
        $phone_number = trim($_POST['phone_number']);
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (!empty($label) && !empty($phone_number)) {
            if ($id > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE whatsapp_numbers SET label = ?, phone_number = ? WHERE id = ?");
                $stmt->execute([$label, $phone_number, $id]);
                log_audit($pdo, 'Update WhatsApp', 'whatsapp_numbers', $id);
                header("Location: /Kamadenu/admin/whatsapp-numbers.php?updated=1");
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO whatsapp_numbers (label, phone_number) VALUES (?, ?)");
                $stmt->execute([$label, $phone_number]);
                log_audit($pdo, 'Create WhatsApp', 'whatsapp_numbers', $pdo->lastInsertId());
                header("Location: /Kamadenu/admin/whatsapp-numbers.php?saved=1");
            }
            exit;
        }
    }
}

// Processing Delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM whatsapp_numbers WHERE id = ?");
        $stmt->execute([$delete_id]);
        log_audit($pdo, 'Delete WhatsApp', 'whatsapp_numbers', $delete_id);
        header("Location: /Kamadenu/admin/whatsapp-numbers.php?deleted=1");
        exit;
    }
}

// Fetch all numbers
$wa_numbers = $pdo->query("SELECT * FROM whatsapp_numbers ORDER BY id ASC")->fetchAll();
?>

<div class="row g-4 mb-4 align-items-center">
    <div class="col-sm-6">
        <h3 class="font-heading mb-0 text-white"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp Directory</h3>
        <p class="text-muted small mb-0">Configure dedicated WhatsApp numbers to assign for Seva, Cattle adoption, store orders, and rescue support.</p>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success border-success-subtle bg-success-subtle text-success p-3 rounded-4"><i class="fas fa-check-circle me-1"></i> WhatsApp contact added successfully!</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success border-success-subtle bg-success-subtle text-success p-3 rounded-4"><i class="fas fa-check-circle me-1"></i> WhatsApp contact details updated!</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-danger border-danger-subtle bg-danger-subtle text-danger p-3 rounded-4"><i class="fas fa-trash-alt me-1"></i> WhatsApp contact removed from directory.</div>
<?php endif; ?>

<div class="row g-4">
    <!-- List of Numbers -->
    <div class="col-lg-8">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3"><i class="fas fa-list me-2 text-warning"></i> Configured Contacts</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contact Label</th>
                            <th>Phone Number</th>
                            <th>Created Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($wa_numbers)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No WhatsApp numbers configured yet. Add one using the form on the right.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($wa_numbers as $wn): ?>
                                <tr>
                                    <td><span class="font-mono text-muted">#<?php echo $wn['id']; ?></span></td>
                                    <td><strong><?php echo e($wn['label']); ?></strong></td>
                                    <td><span class="font-mono badge bg-secondary text-white fs-6 px-3 py-1.5 rounded-pill"><i class="fab fa-whatsapp me-1 text-success"></i> <?php echo e($wn['phone_number']); ?></span></td>
                                    <td class="small text-muted font-mono"><?php echo date('M d, Y', strtotime($wn['created_at'])); ?></td>
                                    <td class="text-end">
                                        <a href="/Kamadenu/admin/whatsapp-numbers.php?edit=<?php echo $wn['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold me-1"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="/Kamadenu/admin/whatsapp-numbers.php?delete=<?php echo $wn['id']; ?>" onclick="return confirm('Are you sure you want to delete this contact? Please make sure no cattle or product uses it.')" class="btn btn-sm btn-outline-danger font-ui fw-bold"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add / Edit Form Card -->
    <div class="col-lg-4">
        <div class="kamadenu-card p-4 border-warning">
            <h4 class="font-heading mb-3 text-warning">
                <i class="fas <?php echo $edit_wn ? 'fa-user-edit' : 'fa-user-plus'; ?> me-2"></i>
                <?php echo $edit_wn ? 'Edit Contact' : 'Add New Contact'; ?>
            </h4>
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <?php if ($edit_wn): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_wn['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Contact Label / Name</label>
                    <input type="text" name="label" class="form-control" placeholder="e.g. Gouseva Helpdesk" value="<?php echo $edit_wn ? e($edit_wn['label']) : ''; ?>" required>
                    <small class="text-muted">Descriptive identifier for assignment fields.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Phone Number (with Country Code)</label>
                    <input type="text" name="phone_number" class="form-control font-mono" placeholder="e.g. +919880012345" value="<?php echo $edit_wn ? e($edit_wn['phone_number']) : ''; ?>" required>
                    <small class="text-muted">Always prefix with country code (e.g., +91 for India). Do not include spaces or symbols.</small>
                </div>

                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold py-2 shadow">
                    <i class="fas fa-save me-1"></i> <?php echo $edit_wn ? 'Update Settings' : 'Add Contact'; ?>
                </button>
                <?php if ($edit_wn): ?>
                    <a href="/Kamadenu/admin/whatsapp-numbers.php" class="btn btn-outline-secondary w-100 mt-2 font-ui">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
