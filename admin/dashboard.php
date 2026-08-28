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

// Low stock items retrieval
$low_stock_items = [];
try {
    $low_stock_items = $pdo->query("SELECT p.name, p.stock_quantity, i.min_threshold FROM products p JOIN inventory i ON p.id = i.product_id WHERE p.stock_quantity <= i.min_threshold")->fetchAll();
} catch (PDOException $e) {
    // Inventory table may not exist or threshold not set; fallback gracefully
}

// Fetch Herd Health Counts dynamically
$health_counts = $pdo->query("SELECT health_status, COUNT(*) as cnt FROM cows GROUP BY health_status")->fetchAll(PDO::FETCH_KEY_PAIR);
$healthy_count = intval(($health_counts['Excellent'] ?? 0) + ($health_counts['Good'] ?? 0));
$treatment_count = intval($health_counts['Under Treatment'] ?? 0);
$critical_count = intval($health_counts['Critical'] ?? 0);

// Fetch Monthly Donations for Chart (last 8 months)
$monthly_donations_query = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as ym,
        DATE_FORMAT(created_at, '%b') as mname,
        SUM(amount) as total
    FROM donations 
    WHERE status = 'Completed'
      AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 MONTH)
    GROUP BY ym, mname
    ORDER BY ym ASC
")->fetchAll(PDO::FETCH_ASSOC);

$chart_labels = [];
$chart_data = [];
$chart_cumulative = [];

for ($i = 7; $i >= 0; $i--) {
    $time = strtotime("-$i months");
    $ym = date('Y-m', $time);
    $mname = date('M', $time);
    $chart_labels[$ym] = $mname;
    $chart_data[$ym] = 0;
}

foreach ($monthly_donations_query as $row) {
    if (isset($chart_data[$row['ym']])) {
        $chart_data[$row['ym']] = floatval($row['total']);
    }
}

$running_sum = 0;
foreach ($chart_data as $ym => $val) {
    $running_sum += $val;
    $chart_cumulative[] = $running_sum;
}

$chart_labels_json = json_encode(array_values($chart_labels));
$chart_data_json = json_encode(array_values($chart_data));
$chart_cumulative_json = json_encode($chart_cumulative);

// Recent Activity Data
$recent_donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC LIMIT 5")->fetchAll();
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
$recent_updates = $pdo->query("SELECT u.*, c.name as cow_name FROM cow_updates u JOIN cows c ON u.cow_id = c.id ORDER BY u.id DESC LIMIT 4")->fetchAll();
?>

<!-- Grand Welcome Hero Banner -->
<div class="admin-welcome-hero mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark font-ui fw-bold px-3 py-1.5 rounded-pill"><i class="fas fa-om me-1"></i> SANCTUARY MANAGEMENT</span>
                <span class="text-warning small font-mono"><i class="fas fa-signal me-1"></i> System Operational</span>
            </div>
            <h2 class="font-heading text-white display-6 mb-2">Welcome Back, <?php echo e($admin['name']); ?>!</h2>
            <p class="text-white-50 font-ui mb-3">Here is the real-time operational overview for Kamadenu Sacred Goushala Trust. Manage cattle details, track financial donations, and process customer store orders seamlessly.</p>
            <div class="d-flex flex-wrap gap-2 align-items-center pt-1">
                <a href="/Kamadhenu-goushala/admin/cow-add.php" class="btn btn-warning rounded-pill font-ui fw-bold px-3.5 py-2 shadow-sm"><i class="fas fa-plus me-1.5"></i> Add Cattles</a>
                <a href="/Kamadhenu-goushala/admin/product-add.php" class="btn btn-outline-light rounded-pill font-ui fw-bold px-3.5 py-2"><i class="fas fa-store me-1.5"></i> Add Store Product</a>
                <a href="/Kamadhenu-goushala/admin/emergency-add.php" class="btn btn-outline-danger rounded-pill font-ui fw-bold px-3.5 py-2"><i class="fas fa-ambulance me-1.5"></i> Rescue Campaign</a>
                <a href="/Kamadhenu-goushala/admin/video-add.php" class="btn btn-outline-info rounded-pill font-ui fw-bold px-3.5 py-2"><i class="fab fa-youtube me-1.5 text-danger"></i> Add Video Link</a>
                <a href="/Kamadhenu-goushala/admin/reports.php" class="btn btn-dark border-secondary rounded-pill font-ui fw-bold px-3.5 py-2 ms-auto"><i class="fas fa-file-download me-1.5 text-warning"></i> Financial Reports</a>
            </div>
        </div>
        <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0 d-none d-lg-block">
            <div class="p-3 rounded-4 bg-black bg-opacity-25 border border-warning border-opacity-25 d-inline-block text-start" style="min-width: 220px;">
                <div class="small text-muted font-ui mb-1"><i class="fas fa-calendar-day me-1 text-warning"></i> Today's Date</div>
                <div class="font-mono fs-5 text-warning fw-bold mb-2"><?php echo date('M d, Y'); ?></div>
                <div class="small text-muted font-ui mb-1"><i class="fas fa-hand-holding-heart me-1 text-success"></i> This Month Revenue</div>
                <div class="font-mono fs-4 text-white fw-bold">₹<?php echo number_format($sum_monthly_donations); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- 4 Master KPI Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="/Kamadhenu-goushala/admin/cows.php" class="text-decoration-none">
            <div class="admin-stat-box d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-ui fw-bold text-uppercase d-block mb-1 tracking-wider">Protected Cattle</span>
                    <div class="fs-1 fw-bold text-white font-mono mb-1"><?php echo $count_cows; ?></div>
                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2.5 py-1 small"><i class="fas fa-shield-alt me-1"></i> Safe Sanctuary</span>
                </div>
                <div class="admin-stat-icon-wrapper bg-grad-amber">
                    <i class="fas fa-cow"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="/Kamadhenu-goushala/admin/sponsors.php" class="text-decoration-none">
            <div class="admin-stat-box d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-ui fw-bold text-uppercase d-block mb-1 tracking-wider">Active Adoptions</span>
                    <div class="fs-1 fw-bold text-white font-mono mb-1"><?php echo $count_sponsors; ?></div>
                    <span class="badge bg-emerald-subtle text-emerald border border-success rounded-pill px-2.5 py-1 small"><i class="fas fa-heart me-1"></i> Sustaining Donors</span>
                </div>
                <div class="admin-stat-icon-wrapper bg-grad-emerald">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="/Kamadhenu-goushala/admin/donations.php" class="text-decoration-none">
            <div class="admin-stat-box d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-ui fw-bold text-uppercase d-block mb-1 tracking-wider">Verified Revenue</span>
                    <div class="fs-2 fw-bold text-white font-mono mb-1">₹<?php echo number_format($sum_donations); ?></div>
                    <span class="badge bg-info-subtle text-info border border-info rounded-pill px-2.5 py-1 small"><i class="fas fa-check-circle me-1"></i> 80G Tax Exempt</span>
                </div>
                <div class="admin-stat-icon-wrapper bg-grad-blue">
                    <i class="fas fa-donate"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="/Kamadhenu-goushala/admin/inventory.php" class="text-decoration-none">
            <div class="admin-stat-box d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-ui fw-bold text-uppercase d-block mb-1 tracking-wider">Low Stock Alerts</span>
                    <div class="fs-1 fw-bold text-danger font-mono mb-1"><?php echo count($low_stock_items); ?></div>
                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-2.5 py-1 small"><i class="fas fa-exclamation-triangle me-1"></i> Stock Replenish</span>
                </div>
                <div class="admin-stat-icon-wrapper bg-grad-rose">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Attention Required Action Items -->
<?php if (!empty($low_stock_items) || $count_volunteers > 0 || $count_orders > 0 || $count_pending_seva > 0): ?>
<div class="admin-dashboard-card mb-4 border-warning" style="background: rgba(245, 158, 11, 0.06) !important;">
    <div class="d-flex align-items-center mb-2">
        <span class="badge bg-warning text-dark font-ui fw-bold px-3 py-1.5 rounded-pill me-2"><i class="fas fa-bell me-1 animate-pulse"></i> ACTION REQUIRED</span>
        <h5 class="font-heading mb-0 text-white">Management Action Items Awaiting Approval</h5>
    </div>
    <ul class="mb-0 small text-white-50 font-ui ps-3">
        <?php if ($count_volunteers > 0): ?>
            <li class="mb-1"><strong><?php echo $count_volunteers; ?></strong> pending volunteer application(s) awaiting review. <a href="/Kamadhenu-goushala/admin/volunteers.php" class="fw-bold text-warning ms-1">Review Applications &rarr;</a></li>
        <?php endif; ?>
        <?php if ($count_orders > 0): ?>
            <li class="mb-1"><strong><?php echo $count_orders; ?></strong> store order(s) currently processing. <a href="/Kamadhenu-goushala/admin/orders.php" class="fw-bold text-info ms-1">Process Orders &rarr;</a></li>
        <?php endif; ?>
        <?php if ($count_pending_seva > 0): ?>
            <li class="mb-1"><strong><?php echo $count_pending_seva; ?></strong> Seva offering(s) scheduled. <a href="/Kamadhenu-goushala/admin/seva.php" class="fw-bold text-success ms-1">View Seva Offerings &rarr;</a></li>
        <?php endif; ?>
        <?php foreach ($low_stock_items as $lsi): ?>
            <li>Low Stock Warning: <strong><?php echo e($lsi['name']); ?></strong> (Current Stock: <?php echo $lsi['stock_quantity']; ?>). <a href="/Kamadhenu-goushala/admin/inventory.php" class="fw-bold text-danger ms-1">Replenish Inventory &rarr;</a></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Visual Analytics Dashboard Grid -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="admin-dashboard-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="font-heading mb-0 text-white"><i class="fas fa-chart-line me-2 text-warning"></i> Revenue & Donation Analytics (INR)</h5>
                    <small class="text-muted font-ui">Monthly donation revenue trends and running fiscal year sum</small>
                </div>
                <span class="badge bg-success text-dark font-mono px-3 py-1.5 rounded-pill fw-bold">This Month: ₹<?php echo number_format($sum_monthly_donations); ?></span>
            </div>
            <canvas id="donationChart" height="230"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-dashboard-card h-100">
            <div class="mb-3">
                <h5 class="font-heading mb-0 text-white"><i class="fas fa-chart-pie me-2 text-warning"></i> Herd Health Overview</h5>
                <small class="text-muted font-ui">Veterinary status of sanctuary cows</small>
            </div>
            <canvas id="healthChart" height="230"></canvas>
        </div>
    </div>
</div>

<!-- Bottom Layout Grid: Tables on Left, Live Logs on Right -->
<div class="row g-4 mb-4">
    <!-- Left Column: Tables (col-lg-8) -->
    <div class="col-lg-8">
        <div class="row g-4">
            <!-- Donations Table -->
            <div class="col-12">
                <div class="admin-dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-heading mb-0 text-white"><i class="fas fa-heart text-danger me-2"></i> Recent Verified Donations</h5>
                        <a href="/Kamadhenu-goushala/admin/donations.php" class="small text-warning font-ui fw-bold text-decoration-none">View All &rarr;</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-custom-table align-middle small">
                            <thead>
                                <tr>
                                    <th>Donor Name</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_donations as $rd): ?>
                                    <tr>
                                        <td><strong class="text-white"><?php echo e($rd['donor_name']); ?></strong></td>
                                        <td class="font-mono fw-bold text-success">₹<?php echo number_format($rd['amount']); ?></td>
                                        <td><span class="badge bg-warning-subtle text-dark fw-bold"><?php echo e($rd['purpose']); ?></span></td>
                                        <td><span class="badge bg-dark font-mono text-warning border border-warning"><?php echo e($rd['receipt_number']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Store Orders Table -->
            <div class="col-12">
                <div class="admin-dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-heading mb-0 text-white"><i class="fas fa-shopping-bag text-info me-2"></i> Recent Customer Store Orders</h5>
                        <a href="/Kamadhenu-goushala/admin/orders.php" class="small text-warning font-ui fw-bold text-decoration-none">View All &rarr;</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-custom-table align-middle small">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $ro): ?>
                                    <tr>
                                        <td><span class="badge bg-dark text-info font-mono border border-info"><?php echo e($ro['order_code']); ?></span></td>
                                        <td class="text-white"><?php echo e($ro['customer_name']); ?></td>
                                        <td class="font-mono fw-bold text-warning">₹<?php echo number_format($ro['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $ro['order_status'] === 'Delivered' ? 'bg-success' : 'bg-warning text-dark'; ?> px-2.5 py-1">
                                                <?php echo e($ro['order_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sanctuary Logs Timeline (col-lg-4) -->
    <div class="col-lg-4">
        <div class="admin-dashboard-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-heading mb-0 text-white"><i class="fas fa-history text-warning me-2"></i> Sanctuary Live Logs</h5>
                    <a href="/Kamadhenu-goushala/admin/cows.php" class="small text-warning font-ui fw-bold text-decoration-none">Cattles &rarr;</a>
                </div>
                <div class="timeline-wrapper mt-3">
                    <?php if (empty($recent_updates)): ?>
                        <div class="text-center py-4 text-muted small font-ui">No recent sanctuary updates.</div>
                    <?php else: ?>
                        <?php foreach ($recent_updates as $ru): ?>
                            <?php 
                                $badge_class = '';
                                if (stripos($ru['title'], 'critical') !== false || stripos($ru['update_text'], 'sick') !== false || stripos($ru['update_text'], 'icu') !== false) {
                                    $badge_class = 'critical';
                                } elseif (stripos($ru['title'], 'health') !== false || stripos($ru['update_text'], 'excellent') !== false || stripos($ru['update_text'], 'recovered') !== false) {
                                    $badge_class = 'health';
                                }
                            ?>
                            <div class="timeline-item">
                                <div class="timeline-badge <?php echo $badge_class; ?>"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-warning-subtle text-dark fw-bold font-ui px-2 py-0.5 rounded" style="font-size: 0.72rem;"><?php echo e($ru['cow_name']); ?></span>
                                        <small class="text-muted font-mono" style="font-size: 0.72rem;"><?php echo e($ru['update_month']); ?> <?php echo e($ru['update_year']); ?></small>
                                    </div>
                                    <h6 class="text-white font-heading mb-1" style="font-size: 0.88rem;"><?php echo e($ru['title']); ?></h6>
                                    <p class="text-muted font-ui mb-0 text-truncate" style="font-size: 0.78rem;" title="<?php echo e($ru['update_text']); ?>"><?php echo e($ru['update_text']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="border-top border-secondary border-opacity-25 pt-3 mt-3">
                <a href="/Kamadhenu-goushala/admin/cows.php" class="btn btn-outline-warning btn-sm w-100 rounded-pill font-ui fw-bold">Manage Cattle Profiles</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

<!-- Chart.js Render Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart !== 'undefined') {
        // 1. Revenue & Donation Analytics Chart
        const ctxDonation = document.getElementById('donationChart').getContext('2d');
        const donationGradient = ctxDonation.createLinearGradient(0, 0, 0, 300);
        donationGradient.addColorStop(0, '#F59E0B');
        donationGradient.addColorStop(1, '#D97706');

        new Chart(ctxDonation, {
            data: {
                labels: <?php echo $chart_labels_json; ?>,
                datasets: [
                    {
                        type: 'line',
                        label: 'Cumulative Total (₹)',
                        data: <?php echo $chart_cumulative_json; ?>,
                        borderColor: '#10B981',
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                        yAxisID: 'y1'
                    },
                    {
                        type: 'bar',
                        label: 'Monthly Revenue (₹)',
                        data: <?php echo $chart_data_json; ?>,
                        backgroundColor: donationGradient,
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: { color: '#F8FAFC', font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': ₹' + context.raw.toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#94A3B8', font: { family: 'Plus Jakarta Sans' } },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            color: '#94A3B8',
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function(value) { return '₹' + value.toLocaleString('en-IN'); }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        ticks: {
                            color: '#10B981',
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function(value) { return '₹' + value.toLocaleString('en-IN'); }
                        },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });

        // 2. Herd Health Overview Doughnut Chart with Center Text
        const ctxHealth = document.getElementById('healthChart').getContext('2d');
        
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                const width = chart.width;
                const height = chart.height;
                const ctx = chart.ctx;
                ctx.restore();
                
                const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                
                ctx.font = "bold 22px 'Outfit', sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#FFFFFF";
                
                const text = total.toString();
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2 - 10;
                ctx.fillText(text, textX, textY);
                
                ctx.font = "600 11px 'Plus Jakarta Sans', sans-serif";
                ctx.fillStyle = "#94A3B8";
                const textSub = "CATTLE";
                const textSubX = Math.round((width - ctx.measureText(textSub).width) / 2);
                const textSubY = height / 2 + 12;
                ctx.fillText(textSub, textSubX, textSubY);
                
                ctx.save();
            }
        };

        new Chart(ctxHealth, {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: ['Excellent/Good', 'Under Treatment', 'Critical'],
                datasets: [{
                    data: [<?php echo $healthy_count; ?>, <?php echo $treatment_count; ?>, <?php echo $critical_count; ?>],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 3,
                    borderColor: '#131B2E'
                }]
            },
            options: {
                responsive: true,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#F8FAFC', padding: 14, font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });
    }
});
</script>


