<?php
require_once __DIR__ . '/header.php';

// Handle Stock Replenishment
if (isset($_POST['product_id']) && isset($_POST['add_quantity'])) {
    $prod_id = intval($_POST['product_id']);
    $add_qty = intval($_POST['add_quantity']);

    $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?")->execute([$add_qty, $prod_id]);
    $pdo->prepare("UPDATE inventory SET current_stock = current_stock + ? WHERE product_id = ?")->execute([$add_qty, $prod_id]);
    $pdo->prepare("INSERT INTO inventory_transactions (product_id, transaction_type, quantity, notes) VALUES (?, 'purchase', ?, 'Stock Replenishment')")->execute([$prod_id, $add_qty]);

    log_audit($pdo, 'Replenish Inventory', 'inventory', $prod_id);
    header("Location: /Kamadhenu-goushala/admin/inventory.php?updated=1");
    exit;
}

$inventory = $pdo->query("SELECT p.id as product_id, p.name as product_name, p.stock_quantity, i.min_threshold, i.max_capacity, i.last_updated FROM products p JOIN inventory i ON p.id = i.product_id ORDER BY p.stock_quantity ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-warehouse text-warning me-2"></i> Inventory Control & Low-Stock Alerts</h3>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Inventory stock updated permanently in MySQL.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Min Threshold Alert</th>
                    <th>Max Capacity</th>
                    <th>Stock Level Status</th>
                    <th>Replenish Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $inv): ?>
                    <tr>
                        <td><strong><?php echo e($inv['product_name']); ?></strong></td>
                        <td class="font-mono fs-5 fw-bold <?php echo $inv['stock_quantity'] <= $inv['min_threshold'] ? 'text-danger' : 'text-success'; ?>"><?php echo $inv['stock_quantity']; ?></td>
                        <td class="font-mono"><?php echo $inv['min_threshold']; ?></td>
                        <td class="font-mono"><?php echo $inv['max_capacity']; ?></td>
                        <td>
                            <?php if ($inv['stock_quantity'] <= $inv['min_threshold']): ?>
                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> LOW STOCK ALERT</span>
                            <?php else: ?>
                                <span class="badge bg-success">Optimal Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="product_id" value="<?php echo $inv['product_id']; ?>">
                                <input type="number" name="add_quantity" class="form-control form-control-sm font-mono" style="width: 80px;" placeholder="+Qty" min="1" required>
                                <button type="submit" class="btn btn-sm btn-kamadenu-primary font-ui fw-bold">+ Add</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
