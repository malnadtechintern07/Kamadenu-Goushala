<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM stories WHERE id = ? OR slug = ?");
$stmt->execute([$id, $id]);
$story = $stmt->fetch();

if (!$story) {
    header("Location: /Kamadenu/stories.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <span class="badge bg-warning text-dark font-mono mb-2"><?php echo date('M d, Y', strtotime($story['published_at'])); ?></span>
        <h1 class="font-heading text-warning mb-1"><?php echo e($story['title']); ?></h1>
        <p class="text-white-50 mb-0">By <?php echo e($story['author']); ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="kamadenu-card p-4 p-md-5">
                    <?php if ($story['image']): ?>
                        <img src="<?php echo e($story['image']); ?>" class="img-fluid rounded-4 mb-4 w-100" style="max-height: 400px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'">
                    <?php endif; ?>
                    <p class="lead fw-bold text-dark mb-4"><?php echo e($story['summary']); ?></p>
                    <div class="lh-lg text-secondary fs-5"><?php echo $story['content']; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
