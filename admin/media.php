<?php
require_once __DIR__ . '/header.php';

$media = $pdo->query("SELECT * FROM media ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-photo-video text-warning me-2"></i> Media & Uploads Manager</h3>
</div>

<div class="kamadenu-card p-4">
    <p class="text-muted">Uploaded photos for cows, products, emergency campaigns, and gallery items.</p>
    <div class="row g-3">
        <?php if (empty($media)): ?>
            <div class="col-12 text-center py-4 text-muted">No uploaded media files. Default assets active.</div>
        <?php else: ?>
            <?php foreach ($media as $m): ?>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center">
                        <img src="/Kamadenu/<?php echo e($m['file_path']); ?>" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                        <small class="d-block font-mono text-truncate"><?php echo e($m['filename']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
