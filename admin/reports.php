<?php
require_once __DIR__ . '/header.php';

$export = isset($_GET['export']) ? $_GET['export'] : '';

if ($export === 'csv_donations') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donations_report_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Donor Name', 'Email', 'Phone', 'Amount', 'Purpose', 'Status', 'Receipt Number', 'Date']);
    $rows = $pdo->query("SELECT id, donor_name, donor_email, donor_phone, amount, purpose, status, receipt_number, created_at FROM donations ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    exit;
}

if ($export === 'csv_cows') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="cows_inventory_report_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Cow ID', 'Cow Code', 'Name', 'Breed', 'Age Years', 'Gender', 'Weight KG', 'Health Status', 'Adoption Status', 'Monthly Amount']);
    $rows = $pdo->query("SELECT id, cow_code, name, breed, age_years, gender, weight_kg, health_status, adoption_status, monthly_sponsorship_amount FROM cows ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-file-invoice text-warning me-2"></i> Financial & Operational Reports Exporter</h3>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-2"><i class="fas fa-file-csv text-success me-2"></i> Donations Report</h4>
            <p class="text-muted small">Export verified donation transactions, receipts, and donor contact details to CSV format.</p>
            <a href="/Kamadenu/admin/reports.php?export=csv_donations" class="btn btn-success font-ui fw-bold"><i class="fas fa-download me-1"></i> Export Donations CSV</a>
        </div>
    </div>

    <div class="col-md-6">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-2"><i class="fas fa-file-csv text-primary me-2"></i> Cattles &amp; Herd Health Report</h4>
            <p class="text-muted small">Export resident cattles profiles, health status, breed distributions, and monthly sponsorship costs.</p>
            <a href="/Kamadenu/admin/reports.php?export=csv_cows" class="btn btn-primary font-ui fw-bold"><i class="fas fa-download me-1"></i> Export Cattles CSV</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
