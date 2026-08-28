<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cow_id = intval($_POST['cow_id']);
    $last_checkup = $_POST['last_checkup'];
    $health_status = $_POST['health_status'];
    $weight = floatval($_POST['weight']);
    $diet = trim($_POST['diet']);
    $notes = trim($_POST['notes']);

    $stmt = $pdo->prepare("INSERT INTO cow_health (cow_id, last_checkup_date, health_status, weight_kg, dietary_plan, medical_notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cow_id, $last_checkup, $health_status, $weight, $diet, $notes]);

    $pdo->prepare("UPDATE cows SET health_status = ?, weight_kg = ? WHERE id = ?")->execute([$health_status, $weight, $cow_id]);

    log_audit($pdo, 'Record Cow Health Checkup', 'cow_health', $cow_id);
    header("Location: /Kamadhenu-goushala/admin/cow-health.php?saved=1");
    exit;
}

$cows = $pdo->query("SELECT id, cow_code, name FROM cows ORDER BY name ASC")->fetchAll();
$logs = $pdo->query("SELECT ch.*, c.name as cow_name, c.cow_code FROM cow_health ch JOIN cows c ON ch.cow_id = c.id ORDER BY ch.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-stethoscope text-warning me-2"></i> Veterinary Cow Health & Medical Logs</h3>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">Health checkup logged permanently in MySQL.</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Record Veterinary Checkup</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Select Cow</label>
                    <select name="cow_id" class="form-select" required>
                        <?php foreach ($cows as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo e($c['cow_code']); ?> - <?php echo e($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Checkup Date</label>
                    <input type="date" name="last_checkup" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Health Status</label>
                    <select name="health_status" class="form-select">
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Under Treatment">Under Treatment</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Current Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" value="380">
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Dietary Plan</label>
                    <input type="text" name="diet" class="form-control" placeholder="Fresh Napier grass, jaggery, minerals...">
                </div>
                <div class="mb-4">
                    <label class="form-label font-ui small fw-bold">Veterinary Medical Notes</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Vaccination and health observations..."></textarea>
                </div>
                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">Save Health Checkup</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Recent Health Log History</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cow</th>
                            <th>Status</th>
                            <th>Weight</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $l): ?>
                            <tr>
                                <td class="font-mono"><?php echo e($l['last_checkup_date']); ?></td>
                                <td><strong><?php echo e($l['cow_name']); ?></strong> (<?php echo e($l['cow_code']); ?>)</td>
                                <td><span class="badge bg-success"><?php echo e($l['health_status']); ?></span></td>
                                <td class="font-mono"><?php echo $l['weight_kg']; ?> kg</td>
                                <td><?php echo e($l['medical_notes']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
