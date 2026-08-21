<?php
require_once __DIR__ . '/includes/header.php';
if (!is_user_logged_in()) { header("Location: /Kamadenu/login.php"); exit; }
$user = current_user($pdo);
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1">My Gouseva Impact & Points</h1>
        <p class="text-white-50 mb-0">Visual summary of your devotional contributions and earned rewards.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="kamadenu-card p-5">
                    <div class="display-3 font-mono fw-bold text-warning mb-2"><?php echo $user['gouseva_points']; ?></div>
                    <h3 class="font-heading mb-3">Total Gouseva Points Earned</h3>
                    <p class="text-muted mb-4">Your points increase with every donation, cow adoption, Seva sponsorship, and volunteer activity.</p>

                    <div class="devotional-phrase fs-3 text-warning">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
