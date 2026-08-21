<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$admin = current_admin($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Kamadenu Goushala</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Kamadenu/css/style.css">
</head>
<body class="bg-light">

<div class="d-flex">
    <!-- Sidebar Navigation -->
    <div class="admin-sidebar p-3 shadow-lg" style="width: 260px; flex-shrink: 0;">
        <a href="/Kamadenu/admin/dashboard.php" class="d-flex align-items-center text-white text-decoration-none mb-4 pb-3 border-bottom border-secondary">
            <i class="fas fa-cow text-warning fs-3 me-2"></i>
            <div>
                <strong class="font-heading fs-5 d-block lh-1">Kamadenu</strong>
                <small class="text-warning font-ui">Admin Console</small>
            </div>
        </a>

        <div class="nav flex-column font-ui small">
            <a href="/Kamadenu/admin/dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt me-2 text-warning"></i> Dashboard</a>
            <a href="/Kamadenu/admin/cows.php" class="nav-link"><i class="fas fa-cow me-2 text-warning"></i> Cow Management</a>
            <a href="/Kamadenu/admin/sponsors.php" class="nav-link"><i class="fas fa-hand-holding-heart me-2 text-warning"></i> Sponsors & Adoptions</a>
            <a href="/Kamadenu/admin/donations.php" class="nav-link"><i class="fas fa-donate me-2 text-warning"></i> Donations & Receipts</a>
            <a href="/Kamadenu/admin/seva.php" class="nav-link"><i class="fas fa-pray me-2 text-warning"></i> Seva Management</a>
            <a href="/Kamadenu/admin/products.php" class="nav-link"><i class="fas fa-store me-2 text-warning"></i> Store Products</a>
            <a href="/Kamadenu/admin/orders.php" class="nav-link"><i class="fas fa-box me-2 text-warning"></i> Product Orders</a>
            <a href="/Kamadenu/admin/inventory.php" class="nav-link"><i class="fas fa-warehouse me-2 text-warning"></i> Inventory Control</a>
            <a href="/Kamadenu/admin/emergency.php" class="nav-link"><i class="fas fa-exclamation-triangle me-2 text-danger"></i> Emergency Relief</a>
            <a href="/Kamadenu/admin/events.php" class="nav-link"><i class="fas fa-calendar-alt me-2 text-warning"></i> Trust Events</a>
            <a href="/Kamadenu/admin/volunteers.php" class="nav-link"><i class="fas fa-hands-helping me-2 text-warning"></i> Volunteers</a>
            <a href="/Kamadenu/admin/reports.php" class="nav-link"><i class="fas fa-file-invoice me-2 text-warning"></i> Reports (PDF/CSV)</a>
            <a href="/Kamadenu/admin/audit-logs.php" class="nav-link"><i class="fas fa-history me-2 text-warning"></i> Audit Logs</a>
            <a href="/Kamadenu/admin/settings.php" class="nav-link"><i class="fas fa-cog me-2 text-warning"></i> System Settings</a>
            
            <hr class="border-secondary my-3">
            <a href="/Kamadenu/index.php" target="_blank" class="nav-link text-light"><i class="fas fa-external-link-alt me-2 text-info"></i> Public Website</a>
            <a href="/Kamadenu/admin/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1 p-4" style="min-width: 0;">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
            <h4 class="font-heading mb-0">Management Portal</h4>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-warning text-dark font-ui fw-bold px-3 py-2"><i class="fas fa-shield-alt me-1"></i> <?php echo e($admin['role_display']); ?></span>
                <span class="font-ui fw-bold text-dark"><i class="fas fa-user-circle text-warning me-1"></i> <?php echo e($admin['name']); ?></span>
            </div>
        </header>
