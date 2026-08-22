<?php
require_once __DIR__ . '/header.php';

$seva_items = $pdo->query("SELECT * FROM seva ORDER BY id ASC")->fetchAll();
$seva_logs = $pdo->query("SELECT sl.*, s.title as seva_title, c.name as cow_name FROM seva_logs sl JOIN seva s ON sl.seva_id = s.id LEFT JOIN cows c ON sl.cow_id = c.id ORDER BY sl.id DESC LIMIT 15")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-pray text-warning me-2"></i> Daily Seva Management & Catalog</h3>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Seva activity updated in MySQL database.</div>
<?php endif; ?>

<!-- Seva Catalog Editor Table -->
<div class="kamadenu-card p-4 mb-4">
    <h4 class="font-heading mb-3">Seva Catalog Items</h4>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Suggested Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seva_items as $si): ?>
                    <tr>
                        <td><strong><?php echo e($si['title']); ?></strong> <?php if ($si['title_kn']) echo "<small class='kn-text text-warning'>({$si['title_kn']})</small>"; ?></td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning"><?php echo e($si['category']); ?></span></td>
                        <td class="font-mono fw-bold">₹<?php echo number_format($si['suggested_amount']); ?></td>
                        <td><span class="badge <?php echo $si['is_active'] ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $si['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                        <td>
                            <a href="/Kamadenu/admin/seva-edit.php?id=<?php echo $si['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit Seva</a>
                            <button onclick="deleteAdminItem('seva', <?php echo $si['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Seva Logs Table -->
<div class="kamadenu-card p-4">
    <h4 class="font-heading mb-3">Recent Seva Logs</h4>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Seva Title</th>
                    <th>Sponsor Name</th>
                    <th>Beneficiary Cow</th>
                    <th>Date Performed</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seva_logs as $sl): ?>
                    <tr>
                        <td class="font-mono small">#SL-<?php echo $sl['id']; ?></td>
                        <td><strong><?php echo e($sl['seva_title']); ?></strong></td>
                        <td><?php echo e($sl['sponsor_name']); ?></td>
                        <td><?php echo $sl['cow_name'] ? e($sl['cow_name']) : 'All Resident Cows'; ?></td>
                        <td class="font-mono small"><?php echo e($sl['date_performed']); ?></td>
                        <td class="font-mono fw-bold">₹<?php echo number_format($sl['amount_paid']); ?></td>
                        <td><span class="badge bg-success"><?php echo e($sl['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
