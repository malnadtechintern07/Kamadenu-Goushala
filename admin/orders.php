<?php
require_once __DIR__ . '/header.php';

// Handle Order Status Update
if (isset($_POST['update_status_id'])) {
    $order_id = intval($_POST['update_status_id']);
    $new_status = $_POST['new_status'];
    $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    log_audit($pdo, 'Update Order Status', 'orders', $order_id);
    header("Location: /Kamadenu/admin/orders.php");
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-box text-warning me-2"></i> Store Orders Management</h3>
</div>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Order Code</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Fulfillment Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><span class="badge bg-dark font-mono"><?php echo e($o['order_code']); ?></span></td>
                        <td><strong><?php echo e($o['customer_name']); ?></strong></td>
                        <td class="small"><?php echo e($o['customer_email']); ?><br><?php echo e($o['customer_phone']); ?></td>
                        <td class="font-mono fw-bold">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td><span class="badge bg-success"><?php echo e($o['payment_status']); ?></span></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="update_status_id" value="<?php echo $o['id']; ?>">
                                <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Processing" <?php echo $o['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Dispatched" <?php echo $o['order_status'] === 'Dispatched' ? 'selected' : ''; ?>>Dispatched</option>
                                    <option value="Delivered" <?php echo $o['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $o['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <span class="small font-mono text-muted"><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
