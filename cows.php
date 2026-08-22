<?php
require_once __DIR__ . '/includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$breed = isset($_GET['breed']) ? trim($_GET['breed']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'id_desc';

$query = "SELECT * FROM cows WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR cow_code LIKE ? OR breed LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($breed)) {
    $query .= " AND breed = ?";
    $params[] = $breed;
}

if (!empty($status)) {
    $query .= " AND adoption_status = ?";
    $params[] = $status;
}

if ($sort === 'name_asc') {
    $query .= " ORDER BY name ASC";
} elseif ($sort === 'age_asc') {
    $query .= " ORDER BY age_years ASC";
} else {
    $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cows = $stmt->fetchAll();

// Get distinct breeds for filter dropdown
$breeds = $pdo->query("SELECT DISTINCT breed FROM cows ORDER BY breed ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="py-4 bg-secondary-subtle border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="font-heading mb-1"><?php echo __t('nav_cows'); ?></h1>
                <p class="text-muted mb-0">Browse and support our indigenous Gir, Sahiwal, Kankrej, Tharparkar, Vechur & Hallikar cattle.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Search & Filter Controls -->
        <form method="GET" action="/Kamadenu/cows.php" class="kamadenu-card p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label font-ui small fw-bold"><?php echo __t('filter_search'); ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="<?php echo __t('search_cow_placeholder'); ?>" value="<?php echo e($search); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-ui small fw-bold"><?php echo __t('filter_breed_label'); ?></label>
                    <select name="breed" class="form-select">
                        <option value=""><?php echo __t('filter_all_breeds'); ?></option>
                        <?php foreach ($breeds as $b): ?>
                            <option value="<?php echo e($b); ?>" <?php echo $breed === $b ? 'selected' : ''; ?>><?php echo e($b); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-ui small fw-bold"><?php echo __t('filter_status_label'); ?></label>
                    <select name="status" class="form-select">
                        <option value=""><?php echo __t('all_statuses'); ?></option>
                        <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>><?php echo __t('status_available'); ?></option>
                        <option value="Sponsored" <?php echo $status === 'Sponsored' ? 'selected' : ''; ?>><?php echo __t('status_sponsored'); ?></option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-semibold"><i class="fas fa-filter me-1"></i> <?php echo __t('filter'); ?></button>
                </div>
            </div>
        </form>

        <!-- Cow List Cards -->
        <div class="row g-4">
            <?php if (empty($cows)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-cow fs-1 text-muted mb-3 d-block"></i>
                    <h4>No cows found matching your criteria.</h4>
                    <a href="/Kamadenu/cows.php" class="btn btn-outline-warning rounded-pill mt-2">Reset Filters</a>
                </div>
            <?php else: ?>
                <?php foreach ($cows as $cow): ?>
                    <?php 
                        $cow_name = __td($cow, 'name');
                        $cow_story = __td($cow, 'rescue_story');
                    ?>
                    <div class="col-md-4">
                        <div class="kamadenu-card h-100 hover-glow">
                            <div class="position-relative">
                                <img src="<?php echo img_url($cow['photo']); ?>" class="cow-card-img" alt="<?php echo e($cow_name); ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
                                <span class="position-absolute top-0 end-0 m-3 badge-cow-code"><?php echo e($cow['cow_code']); ?></span>
                                <span class="position-absolute top-0 start-0 m-3 badge <?php echo $cow['adoption_status'] === 'Sponsored' ? 'bg-secondary' : 'bg-success'; ?> px-3 py-2">
                                    <?php echo $cow['adoption_status'] === 'Sponsored' ? __t('status_sponsored') : __t('status_available'); ?>
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="font-heading fs-4 mb-0"><?php echo e($cow_name); ?></h3>
                                    <span class="badge bg-warning-subtle text-dark border border-warning"><?php echo e($cow['breed']); ?></span>
                                </div>
                                <p class="text-muted small mb-3"><?php echo e(mb_strimwidth($cow_story, 0, 100, '...')); ?></p>

                                <div class="row g-2 small text-muted bg-light p-2 rounded mb-3">
                                    <div class="col-6"><strong><?php echo __t('cow_gender'); ?>:</strong> <?php echo ucfirst($cow['gender']); ?></div>
                                    <div class="col-6"><strong><?php echo __t('cow_age'); ?>:</strong> <?php echo $cow['age_years']; ?> Yrs</div>
                                    <div class="col-6"><strong><?php echo __t('cow_weight'); ?>:</strong> <?php echo $cow['weight_kg']; ?> kg</div>
                                    <div class="col-6"><strong><?php echo __t('cow_health'); ?>:</strong> <span class="text-success fw-bold"><?php echo e($cow['health_status']); ?></span></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        <small class="text-muted d-block"><?php echo __t('cow_monthly_cost'); ?></small>
                                        <span class="fs-5 fw-bold text-dark font-mono">₹<?php echo number_format($cow['monthly_sponsorship_amount']); ?></span>
                                    </div>
                                    <a href="/Kamadenu/cow-detail.php?id=<?php echo $cow['id']; ?>" class="btn btn-kamadenu-primary btn-sm px-3"><?php echo __t('cow_btn_view'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
