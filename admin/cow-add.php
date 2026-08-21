<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cow_code = trim($_POST['cow_code']);
    $name = trim($_POST['name']);
    $name_kn = trim($_POST['name_kn']);
    $name_hi = trim($_POST['name_hi']);
    $breed = trim($_POST['breed']);
    $age_years = intval($_POST['age_years']);
    $gender = $_POST['gender'];
    $weight_kg = floatval($_POST['weight_kg']);
    $monthly_amount = floatval($_POST['monthly_amount']);
    $rescue_date = $_POST['rescue_date'];
    $rescue_story = trim($_POST['rescue_story']);
    $health_status = $_POST['health_status'];

    $stmt = $pdo->prepare("INSERT INTO cows (cow_code, name, name_kn, name_hi, breed, age_years, gender, weight_kg, monthly_sponsorship_amount, rescue_date, rescue_story, health_status, adoption_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
    $stmt->execute([$cow_code, $name, $name_kn, $name_hi, $breed, $age_years, $gender, $weight_kg, $monthly_amount, $rescue_date, $rescue_story, $health_status]);

    log_audit($pdo, 'Add Cow', 'cows', $pdo->lastInsertId());
    header("Location: /Kamadenu/admin/cows.php");
    exit;
}
?>

<h3 class="font-heading mb-4"><i class="fas fa-plus-circle text-warning me-2"></i> Register New Cow Passport</h3>

<div class="kamadenu-card p-4">
    <form method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Unique Code (e.g. KG-007)</label>
                <input type="text" name="cow_code" class="form-control font-mono" placeholder="KG-007" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Cow Name (English)</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Kapila" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Name (Kannada)</label>
                <input type="text" name="name_kn" class="form-control kn-text" placeholder="ಉದಾ. ಕಪಿಲಾ">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Name (Hindi)</label>
                <input type="text" name="name_hi" class="form-control" placeholder="उदा. कपिला">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Breed</label>
                <select name="breed" class="form-select" required>
                    <option value="Gir">Gir</option>
                    <option value="Sahiwal">Sahiwal</option>
                    <option value="Kankrej">Kankrej</option>
                    <option value="Tharparkar">Tharparkar</option>
                    <option value="Vechur">Vechur</option>
                    <option value="Hallikar">Hallikar</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Age (Years)</label>
                <input type="number" name="age_years" class="form-control" value="3" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Gender</label>
                <select name="gender" class="form-select">
                    <option value="female">Female (Cow)</option>
                    <option value="male">Male (Bull)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Weight (kg)</label>
                <input type="number" step="0.1" name="weight_kg" class="form-control" value="350">
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Monthly Care Cost (INR)</label>
                <input type="number" name="monthly_amount" class="form-control font-mono" value="2500" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Rescue Date</label>
                <input type="date" name="rescue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-ui small fw-bold">Initial Health Status</label>
                <select name="health_status" class="form-select">
                    <option value="Excellent">Excellent</option>
                    <option value="Good" selected>Good</option>
                    <option value="Under Treatment">Under Treatment</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label font-ui small fw-bold">Rescue Story Chronicle</label>
                <textarea name="rescue_story" rows="4" class="form-control" placeholder="Detail how the cow was rescued..." required></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Save Cow Passport to MySQL</button>
        <a href="/Kamadenu/admin/cows.php" class="btn btn-outline-secondary font-ui ms-2">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
