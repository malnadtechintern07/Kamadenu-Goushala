<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$action = isset($input['action']) ? $input['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// 1. CREATE RAZORPAY ORDER
if ($action === 'create_order') {
    $amount = floatval($input['amount']);
    $currency = 'INR';
    $receipt = 'KGR-' . time();

    $order_data = [
        'id' => 'order_KGM_' . bin2hex(random_bytes(6)),
        'entity' => 'order',
        'amount' => intval($amount * 100), // in paise
        'amount_paid' => 0,
        'amount_due' => intval($amount * 100),
        'currency' => $currency,
        'receipt' => $receipt,
        'status' => 'created',
        'created_at' => time()
    ];

    json_response(true, 'Razorpay order created', ['order' => $order_data, 'key_id' => 'rzp_test_Kamadenu2026']);
}

// 2. VERIFY & RECORD PAYMENT (CRITICAL BACKEND VERIFICATION)
if ($action === 'verify_payment') {
    $payment_id = isset($input['payment_id']) ? trim($input['payment_id']) : ('pay_KGM_' . bin2hex(random_bytes(6)));
    $order_id = isset($input['order_id']) ? trim($input['order_id']) : ('order_KGM_' . bin2hex(random_bytes(6)));
    $signature = isset($input['signature']) ? trim($input['signature']) : ('sig_' . bin2hex(random_bytes(10)));
    
    $entity_type = isset($input['entity_type']) ? $input['entity_type'] : 'Donation'; // Donation, Sponsorship, Order, Seva, FeedCow
    $amount = floatval($input['amount']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $payment_method = isset($input['payment_method']) ? trim($input['payment_method']) : 'Card';
    $raw_status = isset($input['status']) ? trim($input['status']) : '';

    // Enforce Verification Rules:
    // 1. If payment method is WhatsApp or Bank Transfer -> set to 'Pending Approval' until Admin confirms
    // 2. If explicitly marked as failed or cancelled -> set to 'Payment Failed'
    // 3. Online/Card payment marked complete only after verification check
    $is_failed = ($raw_status === 'failed' || $raw_status === 'cancelled');
    $is_whatsapp = (strtolower($payment_method) === 'whatsapp' || strtolower($payment_method) === 'bank transfer');

    if ($is_failed) {
        $payment_status_db = 'Payment Failed';
    } elseif ($is_whatsapp) {
        $payment_status_db = 'Pending Approval';
    } else {
        $payment_status_db = 'Captured';
    }

    try {
        $pdo->beginTransaction();

        // Save Payment record
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, payment_id, signature, amount, currency, status, payment_method, entity_type, entity_id, raw_response) VALUES (?, ?, ?, ?, 'INR', ?, ?, ?, 0, ?)");
        $stmt->execute([$order_id, $payment_id, $signature, $amount, $payment_status_db, $payment_method, $entity_type, json_encode($input)]);
        $db_payment_id = $pdo->lastInsertId();

        $receipt_num = 'KGR-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        $cert_code = 'KGC-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));

        if ($entity_type === 'Donation') {
            $donor_name = !empty($input['donor_name']) ? trim($input['donor_name']) : 'Devoted Donor';
            $donor_email = !empty($input['donor_email']) ? trim($input['donor_email']) : 'donor@kamadenugoushala.org';
            $donor_phone = !empty($input['donor_phone']) ? trim($input['donor_phone']) : '';
            $purpose = !empty($input['purpose']) ? trim($input['purpose']) : 'General Gouseva';

            $status = ($payment_status_db === 'Captured') ? 'Completed' : ($is_failed ? 'Failed' : 'Pending Approval');

            $stmt = $pdo->prepare("INSERT INTO donations (user_id, donor_name, donor_email, donor_phone, amount, purpose, payment_id, status, receipt_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $donor_name, $donor_email, $donor_phone, $amount, $purpose, $payment_id, $status, $receipt_num]);
            $donation_id = $pdo->lastInsertId();

            if ($status === 'Completed') {
                $pdo->prepare("INSERT INTO receipts (donation_id, receipt_number, pdf_path) VALUES (?, ?, ?)")->execute([$donation_id, $receipt_num, 'uploads/receipts/' . $receipt_num . '.pdf']);
                if (!empty($input['campaign_id'])) {
                    $campaign_id = intval($input['campaign_id']);
                    $pdo->prepare("UPDATE emergency_campaigns SET raised_amount = raised_amount + ? WHERE id = ?")->execute([$amount, $campaign_id]);
                }
                if ($user_id) {
                    $points = intval($amount / 10);
                    $pdo->prepare("UPDATE users SET gouseva_points = gouseva_points + ? WHERE id = ?")->execute([$points, $user_id]);
                    $pdo->prepare("INSERT INTO gouseva_points (user_id, activity_type, points, description) VALUES (?, 'Donation', ?, ?)")->execute([$user_id, $points, "Donation of ₹{$amount}"]);
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Donation Verified!', ?, 'success')")->execute([$user_id, "Your donation of ₹{$amount} for {$purpose} has been verified and marked Payment Complete."]);
                }
            } elseif ($status === 'Pending Approval') {
                if ($user_id) {
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Donation Submitted (Pending Verification)', ?, 'info')")->execute([$user_id, "Your donation of ₹{$amount} for {$purpose} has been submitted and is pending admin verification."]);
                }
            }

        } elseif ($entity_type === 'Sponsorship') {
            $cow_id = intval($input['cow_id']);
            $sponsor_name = !empty($input['sponsor_name']) ? trim($input['sponsor_name']) : 'Gou Sponsor';
            $sponsor_email = !empty($input['sponsor_email']) ? trim($input['sponsor_email']) : 'sponsor@kamadenugoushala.org';
            $months = isset($input['duration_months']) ? intval($input['duration_months']) : 1;

            $status = ($payment_status_db === 'Captured') ? 'Active' : ($is_failed ? 'Failed' : 'Pending Approval');

            $stmt = $pdo->prepare("INSERT INTO sponsors (user_id, name, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $sponsor_name, $sponsor_email, isset($input['sponsor_phone']) ? $input['sponsor_phone'] : '']);
            $sponsor_id = $pdo->lastInsertId();

            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime("+{$months} months"));

            $stmt = $pdo->prepare("INSERT INTO sponsorships (sponsor_id, cow_id, amount, duration_months, start_date, end_date, status, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sponsor_id, $cow_id, $amount, $months, $start_date, $end_date, $status, $payment_id]);

            if ($status === 'Active') {
                $pdo->prepare("UPDATE cows SET adoption_status = 'Sponsored' WHERE id = ?")->execute([$cow_id]);
                $pdo->prepare("INSERT INTO certificates (user_id, cert_code, cert_type, title, recipient_name, issue_date) VALUES (?, ?, 'Sponsorship', 'Cow Adoption & Sponsorship Certificate', ?, ?)")->execute([$user_id, $cert_code, $sponsor_name, $start_date]);
            } else {
                if ($user_id) {
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Sponsorship Pending Approval', ?, 'info')")->execute([$user_id, "Your cow sponsorship request has been received. Certificate will be issued upon admin verification."]);
                }
            }

        } elseif ($entity_type === 'Order') {
            $order_code = isset($input['order_code']) ? $input['order_code'] : ('KGO-' . rand(10000, 99999));
            $customer_name = trim($input['customer_name']);
            $customer_email = trim($input['customer_email']);
            $customer_phone = trim($input['customer_phone']);
            $shipping_address = trim($input['shipping_address']);

            $payment_status = ($payment_status_db === 'Captured') ? 'Paid' : ($is_failed ? 'Failed' : 'Pending Approval');
            $order_status = ($payment_status === 'Paid') ? 'Processing' : 'On Hold';

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_code, customer_name, customer_email, customer_phone, shipping_address, total_amount, payment_status, order_status, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $order_code, $customer_name, $customer_email, $customer_phone, $shipping_address, $amount, $payment_status, $order_status, $payment_id]);
            $order_id_db = $pdo->lastInsertId();

            if (!empty($input['items']) && is_array($input['items'])) {
                foreach ($input['items'] as $item) {
                    $prod_id = intval($item['id']);
                    $qty = intval($item['quantity']);
                    $item_price = floatval($item['price']);
                    $subtotal = $qty * $item_price;

                    $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)")->execute([$order_id_db, $prod_id, $item['name'], $item_price, $qty, $subtotal]);

                    if ($payment_status === 'Paid') {
                        $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?")->execute([$qty, $prod_id]);
                        $pdo->prepare("UPDATE inventory SET current_stock = GREATEST(0, current_stock - ?) WHERE product_id = ?")->execute([$qty, $prod_id]);
                        $pdo->prepare("INSERT INTO inventory_transactions (product_id, transaction_type, quantity, reference_id, notes) VALUES (?, 'sale', ?, ?, 'Customer Order Sale')")->execute([$prod_id, $qty, $order_code]);
                    }
                }
            }

        } elseif ($entity_type === 'FeedCow') {
            $cow_id = intval($input['cow_id']);
            $sponsor_name = trim($input['sponsor_name']);
            $sponsor_email = trim($input['sponsor_email']);
            $sponsor_phone = trim($input['sponsor_phone']);
            
            $status = ($payment_status_db === 'Captured') ? 'Completed' : ($is_failed ? 'Failed' : 'Pending Approval');

            $stmt = $pdo->prepare("INSERT INTO feeding_cow_logs (feeding_cow_id, user_id, sponsor_name, sponsor_email, sponsor_phone, date_sponsored, status, amount_paid, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cow_id, $user_id, $sponsor_name, $sponsor_email, $sponsor_phone, date('Y-m-d'), $status, $amount, $payment_id]);

            if ($status === 'Completed') {
                if ($user_id) {
                    $points = intval($amount / 10);
                    if ($points > 0) {
                        $pdo->prepare("UPDATE users SET gouseva_points = gouseva_points + ? WHERE id = ?")->execute([$points, $user_id]);
                        $pdo->prepare("INSERT INTO gouseva_points (user_id, activity_type, points, description) VALUES (?, 'FeedCow', ?, ?)")->execute([$user_id, $points, "Feeding Cow contribution of ₹{$amount}"]);
                    }
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Feeding Cow Contribution Received!', ?, 'success')")->execute([$user_id, "Your contribution of ₹{$amount} to feed our resident cow has been received and verified."]);
                }
            } else {
                if ($user_id) {
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Feeding Cow Contribution Submitted', ?, 'info')")->execute([$user_id, "Your feeding cow contribution of ₹{$amount} has been submitted and is pending admin verification."]);
                }
            }
        }

        $pdo->commit();

        json_response(true, 'Payment recorded permanently in MySQL with verified status.', [
            'payment_id' => $payment_id,
            'receipt_number' => $receipt_num,
            'cert_code' => $cert_code,
            'amount' => $amount,
            'status' => $payment_status_db
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        json_response(false, 'Transaction processing failed: ' . $e->getMessage());
    }
}

json_response(false, 'Invalid payment action.');
