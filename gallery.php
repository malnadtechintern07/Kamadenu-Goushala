<?php
require_once __DIR__ . '/includes/header.php';

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
if (empty($gallery)) {
    // Seed inline demo items
    $gallery = [
        ['title' => 'Morning Fodder Seva', 'category' => 'Seva', 'image' => 'https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80', 'caption' => 'Devotees offering fresh Napier grass to Gir cows.'],
        ['title' => 'Sacred Gou Aarti', 'category' => 'Worship', 'image' => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=600&q=80', 'caption' => 'Daily evening Aarti ceremony conducted with Vedic chants.'],
        ['title' => 'Rescued Calf Playtime', 'category' => 'Goushala Life', 'image' => 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80', 'caption' => 'Kapila enjoying open pasture sunshine.']
    ];
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_gallery'); ?></h1>
        <p class="text-white-50 mb-0">Visual glimpses of daily Gouseva, sacred ceremonies, and pristine pastures at Kamadenu Goushala Trust.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($gallery as $g): ?>
                <div class="col-md-4">
                    <div class="kamadenu-card h-100 hover-glow">
                        <img src="<?php echo img_url($g['image']); ?>" class="cow-card-img" alt="<?php echo e($g['title']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
                        <div class="card-body p-3">
                            <span class="badge bg-warning-subtle text-dark mb-1"><?php echo e($g['category']); ?></span>
                            <h5 class="font-heading mb-1"><?php echo e($g['title']); ?></h5>
                            <p class="small text-muted mb-0"><?php echo e($g['caption']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
