<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
$stmt->execute([$id]);
$cow = $stmt->fetch();

if (!$cow) {
    echo "<div class='alert alert-danger'>Cow not found</div>";
    require_once __DIR__ . '/footer.php';
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-cow text-warning me-2"></i> Cow Passport (<?php echo e($cow['cow_code']); ?>)</h3>
    <a href="/Kamadenu/admin/cows.php" class="btn btn-outline-secondary font-ui">&larr; Back to List</a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="kamadenu-card p-4 text-center">
            <img src="/Kamadenu/<?php echo e($cow['photo']); ?>" width="100%" height="220" class="rounded object-fit-cover mb-3" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=400&q=80'">
            <h4 class="font-heading mb-1"><?php echo e($cow['name']); ?></h4>
            <span class="badge-cow-code mb-2"><?php echo e($cow['cow_code']); ?></span>
            <div class="badge bg-warning-subtle text-dark border border-warning d-block my-2"><?php echo e($cow['breed']); ?> Breed</div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Passport Information</h4>
            <div class="row g-3">
                <div class="col-6"><strong>Gender:</strong> <?php echo ucfirst($cow['gender']); ?></div>
                <div class="col-6"><strong>Age:</strong> <?php echo $cow['age_years']; ?> Yrs</div>
                <div class="col-6"><strong>Weight:</strong> <?php echo $cow['weight_kg']; ?> kg</div>
                <div class="col-6"><strong>Health Status:</strong> <span class="badge bg-success"><?php echo e($cow['health_status']); ?></span></div>
                <div class="col-6"><strong>Adoption Status:</strong> <span class="badge bg-secondary"><?php echo e($cow['adoption_status']); ?></span></div>
                <div class="col-6"><strong>Monthly Care:</strong> ₹<?php echo number_format($cow['monthly_sponsorship_amount']); ?></div>
                <div class="col-12 border-top pt-3">
                    <strong>Rescue Story Chronicle:</strong>
                    <p class="text-muted mt-1"><?php echo e($cow['rescue_story']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
