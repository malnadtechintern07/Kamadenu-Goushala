<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? OR slug = ?");
$stmt->execute([$id, $id]);
$ev = $stmt->fetch();

if (!$ev) {
    header("Location: /Kamadenu/events.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <span class="badge bg-warning text-dark font-mono mb-2"><i class="fas fa-calendar-alt me-1"></i> <?php echo date('F d, Y', strtotime($ev['event_date'])); ?></span>
        <h1 class="font-heading text-warning mb-1"><?php echo e($ev['title']); ?></h1>
        <?php if ($ev['title_kn']) echo "<p class='kn-text text-warning fs-5 fw-bold mb-0'>{$ev['title_kn']}</p>"; ?>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="<?php echo img_url($ev['photo']); ?>" class="img-fluid rounded-4 shadow-lg border border-warning w-100" style="max-height: 400px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'">
            </div>
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5">
                    <span class="badge bg-success font-ui px-3 py-2 rounded-pill mb-3"><?php echo e($ev['status']); ?> Event</span>
                    <h2 class="font-heading mb-3"><?php echo e($ev['title']); ?></h2>
                    
                    <div class="bg-secondary-subtle p-3 rounded mb-4 font-ui">
                        <div class="mb-2"><i class="fas fa-calendar-day text-warning me-2"></i> <strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($ev['event_date'])); ?></div>
                        <div><i class="fas fa-map-marker-alt text-danger me-2"></i> <strong>Venue:</strong> <?php echo e($ev['venue']); ?></div>
                    </div>

                    <p class="lead text-secondary mb-4"><?php echo e($ev['description']); ?></p>

                    <a href="/Kamadenu/contact.php?event=<?php echo urlencode($ev['title']); ?>" class="btn btn-kamadenu-primary btn-lg w-100 py-3 font-ui fw-bold shadow">
                        <i class="fas fa-hand-holding-heart me-2"></i> Register / Participate in Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
