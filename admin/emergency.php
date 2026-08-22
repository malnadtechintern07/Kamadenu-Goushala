<?php
require_once __DIR__ . '/header.php';

$campaigns = $pdo->query("SELECT * FROM emergency_campaigns ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Emergency Rescue Campaigns</h3>
    <a href="/Kamadenu/admin/emergency-add.php" class="btn btn-danger font-ui fw-bold"><i class="fas fa-plus me-1"></i> Create Emergency Campaign</a>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Emergency relief campaign updated in MySQL database.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Campaign Title</th>
                    <th>Urgency</th>
                    <th>Target Amount</th>
                    <th>Raised Amount</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $c): ?>
                    <tr>
                        <td><strong><?php echo e($c['title']); ?></strong></td>
                        <td><span class="badge bg-danger"><?php echo e($c['urgency_level']); ?></span></td>
                        <td class="font-mono">₹<?php echo number_format($c['target_amount']); ?></td>
                        <td class="font-mono text-success fw-bold">₹<?php echo number_format($c['raised_amount']); ?></td>
                        <td>
                            <?php $pct = min(100, round(($c['raised_amount'] / $c['target_amount']) * 100)); ?>
                            <div class="progress" style="height: 12px; width: 120px;">
                                <div class="progress-bar bg-success" style="width: <?php echo $pct; ?>%;"><?php echo $pct; ?>%</div>
                            </div>
                        </td>
                        <td><span class="badge bg-dark"><?php echo e($c['status']); ?></span></td>
                        <td>
                            <a href="/Kamadenu/admin/emergency-edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit</a>
                            <button onclick="deleteAdminItem('emergency_campaigns', <?php echo $c['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
