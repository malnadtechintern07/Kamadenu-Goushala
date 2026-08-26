<?php
require_once __DIR__ . '/header.php';

$cows = $pdo->query("SELECT fc.*, wn.label as wa_label FROM feeding_cows fc LEFT JOIN whatsapp_numbers wn ON fc.whatsapp_number_id = wn.id ORDER BY fc.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-cookie-bite text-warning me-2"></i> Feeding Cows Directory</h3>
    <a href="/Kamadenu/admin/feed-cow-add.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add Cow for Feeding</a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">Feeding cow registered successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Feeding cow details updated successfully.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Feed Amount</th>
                    <th>Payment Method</th>
                    <th>WhatsApp Link</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cows)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No cows registered for feeding yet. Click "Add Cow for Feeding" to begin.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cows as $c): ?>
                        <tr>
                            <td>
                                <img src="<?php echo img_url($c['photo']); ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'">
                            </td>
                            <td><span class="badge-cow-code"><?php echo e($c['cow_code']); ?></span></td>
                            <td><strong><?php echo e($c['name']); ?></strong></td>
                            <td class="font-mono fw-bold">₹<?php echo number_format($c['feed_amount'], 2); ?></td>
                            <td>
                                <span class="badge bg-info text-capitalize"><?php echo e($c['payment_method']); ?></span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo !empty($c['wa_label']) ? e($c['wa_label']) : 'Default Support'; ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($c['is_available']): ?>
                                    <span class="badge bg-success">Available</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Hidden / Unavailable</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/Kamadenu/admin/feed-cow-edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit &amp; Photo</a>
                                <button onclick="deleteAdminItem('feeding_cows', <?php echo $c['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
