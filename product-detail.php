<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: /Kamadenu/products.php");
    exit;
}
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo e($product['name']); ?></h1>
        <p class="text-white-50 mb-0">Pure A2 & Organic Goushala Product</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>" class="img-fluid rounded-4 shadow-lg border border-warning w-100" style="max-height: 420px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
            </div>
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5">
                    <span class="badge bg-warning-subtle text-dark border border-warning mb-3 px-3 py-2"><?php echo e($product['category_name']); ?></span>
                    <h2 class="font-heading mb-2"><?php echo e($product['name']); ?></h2>
                    <?php if ($product['name_kn']) echo "<p class='kn-text text-warning fs-5 fw-bold mb-3'>{$product['name_kn']}</p>"; ?>

                    <div class="fs-2 fw-bold text-dark font-mono mb-3">₹<?php echo number_format($product['price'], 2); ?></div>

                    <p class="lead text-secondary mb-4"><?php echo e($product['description']); ?></p>

                    <div class="bg-light p-3 rounded mb-4 font-ui">
                        <div><strong>Stock Availability:</strong> <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $product['stock_quantity'] > 0 ? "In Stock ({$product['stock_quantity']} {$product['unit']})" : 'Out of Stock'; ?></span></div>
                        <div><strong>Unit Size:</strong> <?php echo e($product['unit']); ?></div>
                        <div><strong>Method:</strong> Vedic Bilona Hand-Churned Method</div>
                    </div>

                    <div class="d-flex gap-3">
                        <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')" class="btn btn-outline-dark btn-lg flex-fill py-3 font-ui fw-bold shadow-sm">
                            <i class="fas fa-shopping-cart me-2"></i> <?php echo __t('btn_add_to_cart'); ?>
                        </button>
                        <button onclick="buyNow(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')" class="btn btn-warning btn-lg flex-fill py-3 font-ui fw-bold text-dark shadow">
                            <i class="fas fa-bolt me-2"></i> <?php echo __t('btn_buy_now'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
