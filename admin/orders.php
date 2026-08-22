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

// Handle Manual Payment Approval
if (isset($_POST['approve_order_payment_id'])) {
    $order_id = intval($_POST['approve_order_payment_id']);
    
    // Fetch order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND payment_status = 'Pending Approval'");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $pdo->beginTransaction();
        try {
            // Update order status & payment status
            $pdo->prepare("UPDATE orders SET payment_status = 'Paid', order_status = 'Processing' WHERE id = ?")->execute([$order_id]);
            
            // Set payment status to captured in payments table
            $pdo->prepare("UPDATE payments SET status = 'Captured' WHERE payment_id = ?")->execute([$order['payment_id']]);
            
            // Process order items to decrement inventory stock now
            $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt_items->execute([$order_id]);
            $items = $stmt_items->fetchAll();
            
            foreach ($items as $item) {
                $prod_id = intval($item['product_id']);
                $qty = intval($item['quantity']);
                
                // Decrement inventory stock
                $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?")->execute([$qty, $prod_id]);
                $pdo->prepare("UPDATE inventory SET current_stock = GREATEST(0, current_stock - ?) WHERE product_id = ?")->execute([$qty, $prod_id]);
                $pdo->prepare("INSERT INTO inventory_transactions (product_id, transaction_type, quantity, reference_id, notes) VALUES (?, 'sale', ?, ?, 'Customer Order Sale (Approved)')")->execute([$prod_id, $qty, $order['order_code']]);
            }
            
            // Create user notification
            if (!empty($order['user_id'])) {
                $user_id = $order['user_id'];
                $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Order Payment Verified!', ?, 'success')")->execute([$user_id, "Payment for order #{$order['order_code']} has been verified. Your order status is now Processing."]);
            }
            
            log_audit($pdo, 'Approve Order Payment', 'orders', $order_id);
            $pdo->commit();
            
            header("Location: /Kamadenu/admin/orders.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Approval failed: " . $e->getMessage();
        }
    }
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-box text-warning me-2"></i> Store Orders Management</h3>
</div>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
<?php endif; ?>

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
                        <td>
                            <?php if ($o['payment_status'] === 'Pending Approval'): ?>
                                <span class="badge bg-warning text-dark mb-1 d-block"><?php echo e($o['payment_status']); ?></span>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Verify payment and approve this order?');">
                                    <input type="hidden" name="approve_order_payment_id" value="<?php echo $o['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success py-0 px-2 font-ui" style="font-size: 0.75rem;"><i class="fas fa-check"></i> Approve</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success"><?php echo e($o['payment_status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="update_status_id" value="<?php echo $o['id']; ?>">
                                <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()" <?php echo $o['payment_status'] === 'Pending Approval' ? 'disabled' : ''; ?>>
                                    <option value="On Hold" <?php echo $o['order_status'] === 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
                                    <option value="Processing" <?php echo $o['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Dispatched" <?php echo $o['order_status'] === 'Dispatched' ? 'selected' : ''; ?>>Dispatched</option>
                                    <option value="Delivered" <?php echo $o['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $o['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="small font-mono text-muted mb-1"><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></div>
                            <button onclick="deleteAdminItem('orders', <?php echo $o['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
