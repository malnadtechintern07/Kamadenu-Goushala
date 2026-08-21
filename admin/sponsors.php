<?php
require_once __DIR__ . '/header.php';

$sponsorships = $pdo->query("SELECT s.*, sp.name as sponsor_name, sp.email, sp.phone, c.name as cow_name, c.cow_code FROM sponsorships s JOIN sponsors sp ON s.sponsor_id = sp.id JOIN cows c ON s.cow_id = c.id ORDER BY s.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-hand-holding-heart text-warning me-2"></i> Active Sponsors & Cow Adoptions</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Sponsor Name</th>
                    <th>Contact</th>
                    <th>Sponsored Cow</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sponsorships as $sp): ?>
                    <tr>
                        <td><strong><?php echo e($sp['sponsor_name']); ?></strong></td>
                        <td class="small"><?php echo e($sp['email']); ?><br><?php echo e($sp['phone']); ?></td>
                        <td><strong><?php echo e($sp['cow_name']); ?></strong> (<?php echo e($sp['cow_code']); ?>)</td>
                        <td><?php echo $sp['duration_months']; ?> Mos</td>
                        <td class="font-mono small"><?php echo e($sp['start_date']); ?></td>
                        <td class="font-mono small"><?php echo e($sp['end_date']); ?></td>
                        <td class="font-mono fw-bold text-dark">₹<?php echo number_format($sp['amount']); ?></td>
                        <td><span class="badge bg-success"><?php echo e($sp['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
