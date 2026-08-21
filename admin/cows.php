<?php
require_once __DIR__ . '/header.php';

$cows = $pdo->query("SELECT * FROM cows ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-cow text-warning me-2"></i> Resident Cattle & Passports</h3>
    <a href="/Kamadenu/admin/cow-add.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add New Cow Passport</a>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Cow passport and photo updated in MySQL.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Breed</th>
                    <th>Age</th>
                    <th>Health</th>
                    <th>Care Cost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cows as $c): ?>
                    <tr>
                        <td><img src="/Kamadenu/<?php echo e($c['photo']); ?>" width="50" height="50" class="rounded object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'"></td>
                        <td><span class="badge-cow-code"><?php echo e($c['cow_code']); ?></span></td>
                        <td><strong><?php echo e($c['name']); ?></strong></td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning"><?php echo e($c['breed']); ?></span></td>
                        <td><?php echo $c['age_years']; ?> Yrs</td>
                        <td><span class="badge bg-success"><?php echo e($c['health_status']); ?></span></td>
                        <td class="font-mono fw-bold">₹<?php echo number_format($c['monthly_sponsorship_amount']); ?></td>
                        <td><span class="badge <?php echo $c['adoption_status'] === 'Sponsored' ? 'bg-secondary' : 'bg-success'; ?>"><?php echo e($c['adoption_status']); ?></span></td>
                        <td>
                            <a href="/Kamadenu/admin/cow-view.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-dark font-ui me-1"><i class="fas fa-eye"></i> View</a>
                            <a href="/Kamadenu/admin/cow-edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit & Photo</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
