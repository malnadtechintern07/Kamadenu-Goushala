<?php
require_once __DIR__ . '/includes/header.php';

$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$categories = $pdo->query("SELECT * FROM product_categories ORDER BY id ASC")->fetchAll();

$query = "SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id WHERE p.is_active = 1";
$params = [];

if ($cat_id > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $cat_id;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$query .= " ORDER BY p.id ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('products_title'); ?></h1>
                <p class="text-white-50 mb-0"><?php echo __t('products_subtitle'); ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Category Filter & Search Bar -->
        <div class="kamadenu-card p-4 mb-4">
            <form method="GET" action="/Kamadenu/products.php" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="<?php echo __t('search_product_placeholder'); ?>" value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value=""><?php echo __t('all_categories'); ?></option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat_id === $cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold"><?php echo __t('filter'); ?></button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <?php foreach ($products as $p): ?>
                <div class="col-md-4">
                    <div class="kamadenu-card h-100">
                        <img src="<?php echo e($p['image']); ?>" class="cow-card-img" alt="<?php echo e($p['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-warning-subtle text-dark border border-warning mb-2"><?php echo e($p['category_name']); ?></span>
                                <h4 class="font-heading fs-5"><?php echo e($p['name']); ?></h4>
                                <?php if ($p['name_kn']) echo "<p class='kn-text text-warning small fw-bold mb-2'>{$p['name_kn']}</p>"; ?>
                                <p class="small text-muted mb-3"><?php echo e(mb_strimwidth($p['description'], 0, 95, '...')); ?></p>
                            </div>

                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="fs-4 fw-bold text-dark font-mono">₹<?php echo number_format($p['price']); ?></span>
                                        <small class="text-muted d-block font-ui"><?php echo __t('product_stock'); ?>: <?php echo $p['stock_quantity']; ?> <?php echo e($p['unit']); ?></small>
                                    </div>
                                    <a href="/Kamadenu/product-detail.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary font-ui"><i class="fas fa-info-circle"></i> Details</a>
                                </div>

                                <div class="d-flex gap-2">
                                    <button onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>')" class="btn btn-outline-dark btn-sm flex-fill font-ui fw-bold">
                                        <i class="fas fa-shopping-cart me-1"></i> <?php echo __t('btn_add_to_cart'); ?>
                                    </button>
                                    <button onclick="buyNow(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>')" class="btn btn-warning btn-sm flex-fill font-ui fw-bold text-dark shadow-sm">
                                        <i class="fas fa-bolt me-1"></i> <?php echo __t('btn_buy_now'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
