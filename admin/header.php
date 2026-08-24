<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

$admin = current_admin($pdo);
$current_page = basename($_SERVER['SCRIPT_NAME']);

function is_admin_active($page, $current) {
    return ($current === $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Kamadenu Goushala</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Kamadenu/css/style.css">
    <link rel="stylesheet" href="/Kamadenu/css/admin.css">
    <link rel="stylesheet" href="/Kamadenu/css/admin-dashboard.css">
    
    <!-- Favicon Icon -->
    <link rel="icon" type="image/svg+xml" href="/Kamadenu/assets/images/favicon.svg">
</head>
<body class="admin-body">


<div class="d-flex">
    <div class="sidebar-overlay" id="sidebar-overlay-el"></div>
    <!-- Sidebar Navigation -->
    <div class="admin-sidebar-nav p-3 shadow-lg">
        <a href="/Kamadenu/admin/dashboard.php" class="d-flex align-items-center text-white text-decoration-none mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <?php 
            $logo_setting = get_setting($pdo, 'website_logo', '');
            $logo_url = img_url(empty($logo_setting) ? 'assets/images/logo.png' : $logo_setting);
            ?>
            <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Kamadenu Goushala Logo" class="me-2.5" style="height: 48px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));">

            <div>
                <strong class="font-heading fs-5 d-block lh-1 text-warning">Kamadenu</strong>
                <small class="text-white-50 font-ui fs-7 tracking-wider">ADMIN CONSOLE</small>
            </div>
        </a>


        <div class="nav flex-column font-ui small gap-1">
            <div class="nav-section-title">Overview</div>
            <a href="/Kamadenu/admin/dashboard.php" class="nav-link <?php echo is_admin_active('dashboard.php', $current_page); ?>"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></a>
            
            <div class="nav-section-title">Sanctuary &amp; Cattle</div>
            <a href="/Kamadenu/admin/cows.php" class="nav-link <?php echo is_admin_active('cows.php', $current_page); ?>"><i class="fas fa-cow"></i> <span>Cattles</span></a>
            <a href="/Kamadenu/admin/sponsors.php" class="nav-link <?php echo is_admin_active('sponsors.php', $current_page); ?>"><i class="fas fa-hand-holding-heart"></i> <span>Adoptions &amp; Sponsors</span></a>
            <a href="/Kamadenu/admin/donations.php" class="nav-link <?php echo is_admin_active('donations.php', $current_page); ?>"><i class="fas fa-donate"></i> <span>Donations &amp; Receipts</span></a>
            <a href="/Kamadenu/admin/seva.php" class="nav-link <?php echo is_admin_active('seva.php', $current_page); ?>"><i class="fas fa-pray"></i> <span>Sacred Gouseva</span></a>
            
            <div class="nav-section-title">E-Commerce &amp; Inventory</div>
            <a href="/Kamadenu/admin/products.php" class="nav-link <?php echo is_admin_active('products.php', $current_page); ?>"><i class="fas fa-store"></i> <span>Store Products</span></a>
            <a href="/Kamadenu/admin/orders.php" class="nav-link <?php echo is_admin_active('orders.php', $current_page); ?>"><i class="fas fa-box-open"></i> <span>Customer Orders</span></a>
            <a href="/Kamadenu/admin/inventory.php" class="nav-link <?php echo is_admin_active('inventory.php', $current_page); ?>"><i class="fas fa-warehouse"></i> <span>Inventory Control</span></a>
            
            <div class="nav-section-title">Relief &amp; Events</div>
            <a href="/Kamadenu/admin/emergency.php" class="nav-link <?php echo is_admin_active('emergency.php', $current_page); ?>"><i class="fas fa-ambulance"></i> <span>Emergency Relief</span></a>
            <a href="/Kamadenu/admin/events.php" class="nav-link <?php echo is_admin_active('events.php', $current_page); ?>"><i class="fas fa-calendar-alt"></i> <span>Trust Events</span></a>
            <a href="/Kamadenu/admin/videos.php" class="nav-link <?php echo is_admin_active('videos.php', $current_page); ?>"><i class="fab fa-youtube"></i> <span>Program Videos</span></a>
            <a href="/Kamadenu/admin/volunteers.php" class="nav-link <?php echo is_admin_active('volunteers.php', $current_page); ?>"><i class="fas fa-hands-helping"></i> <span>Volunteers</span></a>
            
            <div class="nav-section-title">System &amp; Security</div>
            <a href="/Kamadenu/admin/homepage-settings.php" class="nav-link <?php echo is_admin_active('homepage-settings.php', $current_page); ?>"><i class="fas fa-home"></i> <span>Homepage Manager</span></a>
            <a href="/Kamadenu/admin/about-settings.php" class="nav-link <?php echo is_admin_active('about-settings.php', $current_page); ?>"><i class="fas fa-info-circle"></i> <span>About Us Editor</span></a>
            <a href="/Kamadenu/admin/whatsapp-numbers.php" class="nav-link <?php echo is_admin_active('whatsapp-numbers.php', $current_page); ?>"><i class="fab fa-whatsapp"></i> <span>WhatsApp Directory</span></a>
            <a href="/Kamadenu/admin/reports.php" class="nav-link <?php echo is_admin_active('reports.php', $current_page); ?>"><i class="fas fa-file-invoice-dollar"></i> <span>Reports Export</span></a>
            <a href="/Kamadenu/admin/audit-logs.php" class="nav-link <?php echo is_admin_active('audit-logs.php', $current_page); ?>"><i class="fas fa-history"></i> <span>Audit Logs</span></a>
            <a href="/Kamadenu/admin/settings.php" class="nav-link <?php echo is_admin_active('settings.php', $current_page); ?>"><i class="fas fa-cog"></i> <span>Settings</span></a>
            
            <hr class="border-secondary border-opacity-25 my-3">
            <a href="/Kamadenu/index.php" target="_blank" class="nav-link text-info"><i class="fas fa-external-link-alt"></i> <span>View Public Site</span></a>
            <a href="/Kamadenu/admin/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout Admin</span></a>
        </div>
    </div>

    <div class="flex-grow-1 p-4" style="min-width: 0;">
        <header class="d-flex flex-wrap justify-content-between align-items-center mb-4 admin-header-nav">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-warning d-lg-none" id="sidebar-toggle-btn" aria-label="Toggle Navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="font-heading mb-0 text-white"><i class="fas fa-shield-alt text-warning me-2 animate-pulse"></i> Management Console</h4>
                <span class="badge badge-admin-live-time px-3 py-2 rounded-pill font-mono"><i class="fas fa-clock me-1 text-cyan animate-spin-slow"></i> <span id="admin-live-time">--:--:--</span></span>
            </div>
            
            <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
                <a href="/Kamadenu/admin/cows.php" class="btn btn-sm btn-admin-header-search rounded-pill font-ui fw-bold px-3"><i class="fas fa-search me-1"></i> Search Database</a>
                <span class="badge badge-admin-role-glow font-ui fw-bold px-3 py-2 rounded-pill"><i class="fas fa-user-shield me-1"></i> <?php echo e($admin['role_display']); ?></span>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle text-white font-heading fw-bold d-flex align-items-center justify-content-center shadow-sm admin-profile-avatar" style="width: 38px; height: 38px;">
                        <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
                    </div>
                    <span class="font-ui fw-bold text-white d-none d-md-inline user-name-glow"><?php echo e($admin['name']); ?></span>
                </div>
            </div>
        </header>

        <script>
        function updateAdminTime() {
            const el = document.getElementById('admin-live-time');
            if (el) {
                const now = new Date();
                el.textContent = now.toLocaleTimeString('en-US', { hour12: true });
            }
        }
        setInterval(updateAdminTime, 1000);
        updateAdminTime();
        </script>


