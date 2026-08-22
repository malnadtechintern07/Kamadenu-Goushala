<?php
require_once __DIR__ . '/header.php';

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-store text-warning me-2"></i> Goushala Product Catalog</h3>
    <a href="/Kamadenu/admin/product-add.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add New Product</a>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Product details updated in MySQL database.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="/Kamadenu/<?php echo e($p['image']); ?>" width="50" height="50" class="rounded object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=100&q=80'"></td>
                        <td><strong><?php echo e($p['name']); ?></strong></td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning"><?php echo e($p['category_name']); ?></span></td>
                        <td class="font-mono fw-bold">₹<?php echo number_format($p['price'], 2); ?></td>
                        <td class="font-mono fw-bold <?php echo $p['stock_quantity'] <= 10 ? 'text-danger' : 'text-success'; ?>"><?php echo $p['stock_quantity']; ?></td>
                        <td><?php echo e($p['unit']); ?></td>
                        <td><span class="badge <?php echo $p['is_active'] ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $p['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                        <td>
                            <a href="/Kamadenu/admin/product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit</a>
                            <button onclick="deleteAdminItem('products', <?php echo $p['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
