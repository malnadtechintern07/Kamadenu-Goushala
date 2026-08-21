<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $story = trim($_POST['story']);
    $target_amount = floatval($_POST['target_amount']);
    $urgency_level = $_POST['urgency_level'];
    $start_date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO emergency_campaigns (title, story, target_amount, raised_amount, status, urgency_level, start_date) VALUES (?, ?, ?, 0.00, 'Active', ?, ?)");
    $stmt->execute([$title, $story, $target_amount, $urgency_level, $start_date]);

    log_audit($pdo, 'Create Emergency Campaign', 'emergency_campaigns', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/emergency.php");
    exit;
}
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
            <div class="col-md-6">
                <label class="form-label font-ui small fw-bold">Urgency Level</label>
                <select name="urgency_level" class="form-select">
                    <option value="Critical">Critical</option>
                    <option value="High">High</option>
                    <option value="Normal">Normal</option>
                </select>
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
