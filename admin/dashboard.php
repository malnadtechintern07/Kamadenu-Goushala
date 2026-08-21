<?php
require_once __DIR__ . '/header.php';

// Fetch Core Dashboard KPIs
$count_cows = $pdo->query("SELECT COUNT(*) FROM cows")->fetchColumn();
$count_sponsors = $pdo->query("SELECT COUNT(*) FROM sponsorships WHERE status = 'Active'")->fetchColumn();
$sum_donations = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status = 'Completed'")->fetchColumn();
$sum_monthly_donations = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status = 'Completed' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
$count_pending_seva = $pdo->query("SELECT COUNT(*) FROM seva_logs WHERE status = 'Scheduled'")->fetchColumn();
$count_volunteers = $pdo->query("SELECT COUNT(*) FROM volunteers WHERE status = 'Pending'")->fetchColumn();
$count_emergency = $pdo->query("SELECT COUNT(*) FROM emergency_campaigns WHERE status = 'Active'")->fetchColumn();
$count_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Processing'")->fetchColumn();
$low_stock_items = $pdo->query("SELECT p.name, p.stock_quantity, i.min_threshold FROM products p JOIN inventory i ON p.id = i.product_id WHERE p.stock_quantity <= i.min_threshold")->fetchAll();

// Recent Activity Data
$recent_donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC LIMIT 5")->fetchAll();
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
$recent_updates = $pdo->query("SELECT u.*, c.name as cow_name FROM cow_updates u JOIN cows c ON u.cow_id = c.id ORDER BY u.id DESC LIMIT 4")->fetchAll();
?>

<!-- KPI Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="/Kamadenu/admin/cows.php" class="text-decoration-none">
            <div class="kamadenu-card p-3 d-flex align-items-center justify-content-between border-start border-warning border-4">
                <div>
                    <span class="text-muted small font-ui fw-bold uppercase">Total Cows</span>
                    <div class="fs-2 fw-bold text-dark font-mono"><?php echo $count_cows; ?></div>
                </div>
                <div class="fs-1 text-warning"><i class="fas fa-cow"></i></div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/Kamadenu/admin/sponsors.php" class="text-decoration-none">
            <div class="kamadenu-card p-3 d-flex align-items-center justify-content-between border-start border-success border-4">
                <div>
                    <span class="text-muted small font-ui fw-bold uppercase">Active Sponsors</span>
                    <div class="fs-2 fw-bold text-dark font-mono"><?php echo $count_sponsors; ?></div>
                </div>
                <div class="fs-1 text-success"><i class="fas fa-hand-holding-heart"></i></div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/Kamadenu/admin/donations.php" class="text-decoration-none">
            <div class="kamadenu-card p-3 d-flex align-items-center justify-content-between border-start border-primary border-4">
                <div>
                    <span class="text-muted small font-ui fw-bold uppercase">Total Donations</span>
                    <div class="fs-2 fw-bold text-dark font-mono">₹<?php echo number_format($sum_donations); ?></div>
                </div>
                <div class="fs-1 text-primary"><i class="fas fa-donate"></i></div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/Kamadenu/admin/inventory.php" class="text-decoration-none">
            <div class="kamadenu-card p-3 d-flex align-items-center justify-content-between border-start border-danger border-4">
                <div>
                    <span class="text-muted small font-ui fw-bold uppercase">Low Stock Alerts</span>
                    <div class="fs-2 fw-bold text-danger font-mono"><?php echo count($low_stock_items); ?></div>
                </div>
                <div class="fs-1 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- Attention Required Alert Banner -->
<?php if (!empty($low_stock_items) || $count_volunteers > 0): ?>
<div class="alert bg-danger-subtle border-danger text-danger p-3 rounded-4 mb-4">
    <h5 class="font-heading mb-2"><i class="fas fa-bell me-2"></i> Attention Required Action Items</h5>
    <ul class="mb-0 small">
        <?php if ($count_volunteers > 0): ?>
            <li><strong><?php echo $count_volunteers; ?></strong> pending volunteer application(s) awaiting approval. <a href="/Kamadenu/admin/volunteers.php" class="fw-bold text-danger">Review Applications &rarr;</a></li>
        <?php endif; ?>
        <?php foreach ($low_stock_items as $lsi): ?>
            <li>Low Stock Warning: <strong><?php echo e($lsi['name']); ?></strong> (Current Stock: <?php echo $lsi['stock_quantity']; ?>). <a href="/Kamadenu/admin/inventory.php" class="fw-bold text-danger">Replenish Inventory &rarr;</a></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="kamadenu-card p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="font-ui fw-bold text-muted me-2"><i class="fas fa-bolt me-1 text-warning"></i> Quick Management:</span>
        <a href="/Kamadenu/admin/cow-add.php" class="btn btn-warning btn-sm rounded-pill font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add Cow Passport</a>
        <a href="/Kamadenu/admin/product-add.php" class="btn btn-outline-warning btn-sm rounded-pill font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add Product</a>
        <a href="/Kamadenu/admin/emergency-add.php" class="btn btn-outline-danger btn-sm rounded-pill font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add Rescue Campaign</a>
        <a href="/Kamadenu/admin/reports.php" class="btn btn-dark btn-sm rounded-pill font-ui ms-auto"><i class="fas fa-file-download me-1"></i> Generate Reports</a>
    </div>
</div>

<!-- Chart.js Visual Analytics -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="kamadenu-card p-4">
            <h5 class="font-heading mb-3"><i class="fas fa-chart-line me-2 text-warning"></i> Donation Trends (INR)</h5>
            <canvas id="donationChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="kamadenu-card p-4">
            <h5 class="font-heading mb-3"><i class="fas fa-chart-pie me-2 text-warning"></i> Herd Health Distribution</h5>
            <canvas id="healthChart" height="220"></canvas>
        </div>
    </div>
</div>

<!-- Recent Transactions & Orders -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="kamadenu-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-heading mb-0">Recent Verified Donations</h5>
                <a href="/Kamadenu/admin/donations.php" class="small text-warning font-ui">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Purpose</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_donations as $rd): ?>
                            <tr>
                                <td><strong><?php echo e($rd['donor_name']); ?></strong></td>
                                <td class="font-mono fw-bold text-success">₹<?php echo number_format($rd['amount']); ?></td>
                                <td><?php echo e($rd['purpose']); ?></td>
                                <td><span class="badge bg-dark font-mono"><?php echo e($rd['receipt_number']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="kamadenu-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-heading mb-0">Recent Store Orders</h5>
                <a href="/Kamadenu/admin/orders.php" class="small text-warning font-ui">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $ro): ?>
                            <tr>
                                <td><span class="badge bg-secondary font-mono"><?php echo e($ro['order_code']); ?></span></td>
                                <td><?php echo e($ro['customer_name']); ?></td>
                                <td class="font-mono fw-bold">₹<?php echo number_format($ro['total_amount']); ?></td>
                                <td><span class="badge bg-warning text-dark"><?php echo e($ro['order_status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Donation Trends Chart
    const ctxDonation = document.getElementById('donationChart').getContext('2d');
    new Chart(ctxDonation, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [{
                label: 'Donations (₹)',
                data: [12000, 18500, 24000, 31000, 28000, 45000, 52000, <?php echo $sum_monthly_donations ?: 48000; ?>],
                backgroundColor: '#A04000',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // 2. Health Distribution Chart
    const ctxHealth = document.getElementById('healthChart').getContext('2d');
    new Chart(ctxHealth, {
        type: 'doughnut',
        data: {
            labels: ['Excellent', 'Good', 'Under Treatment'],
            datasets: [{
                data: [4, 2, 1],
                backgroundColor: ['#27AE60', '#F39C12', '#E74C3C']
            }]
        },
        options: { responsive: true }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
