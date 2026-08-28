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
    <title>Admin Dashboard | Kamadenu Goushala Trust</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Kamadhenu-goushala/css/style.css">
    <link rel="stylesheet" href="/Kamadhenu-goushala/css/admin.css">
    <link rel="stylesheet" href="/Kamadhenu-goushala/css/admin-dashboard.css">
    
    <!-- Favicon Icon -->
    <link rel="icon" type="image/svg+xml" href="/Kamadhenu-goushala/assets/images/favicon.svg">
</head>
<body class="admin-body">


<div class="d-flex">
    <div class="sidebar-overlay" id="sidebar-overlay-el"></div>
    <!-- Sidebar Navigation -->
    <div class="admin-sidebar-nav p-3 shadow-lg">
        <a href="/Kamadhenu-goushala/admin/dashboard.php" class="d-flex align-items-center text-white text-decoration-none mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <?php 
            $logo_setting = get_setting($pdo, 'website_logo', '');
            $logo_url = img_url(empty($logo_setting) ? 'assets/images/logo.png' : $logo_setting);
            ?>
            <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Kamadenu Goushala Trust Logo" class="me-2.5" style="height: 48px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));">

            <div>
                <strong class="font-heading fs-5 d-block lh-1 text-warning">Kamadenu</strong>
                <small class="text-white-50 font-ui fs-7 tracking-wider">ADMIN CONSOLE</small>
            </div>
        </a>


        <div class="nav flex-column font-ui small gap-1">
            <div class="nav-section-title">Overview</div>
            <a href="/Kamadhenu-goushala/admin/dashboard.php" class="nav-link <?php echo is_admin_active('dashboard.php', $current_page); ?>"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></a>
            
            <div class="nav-section-title">Sanctuary &amp; Cattle</div>
            <a href="/Kamadhenu-goushala/admin/cows.php" class="nav-link <?php echo is_admin_active('cows.php', $current_page); ?>"><i class="fas fa-cow"></i> <span>Cattles</span></a>
            <a href="/Kamadhenu-goushala/admin/sponsors.php" class="nav-link <?php echo is_admin_active('sponsors.php', $current_page); ?>"><i class="fas fa-hand-holding-heart"></i> <span>Adoptions &amp; Sponsors</span></a>
            <a href="/Kamadhenu-goushala/admin/feed.php" class="nav-link <?php echo is_admin_active('feed.php', $current_page); ?>"><i class="fas fa-cookie-bite"></i> <span>Feed Cow Manager</span></a>
            <a href="/Kamadhenu-goushala/admin/donations.php" class="nav-link <?php echo is_admin_active('donations.php', $current_page); ?>"><i class="fas fa-donate"></i> <span>Donations &amp; Receipts</span></a>
            <a href="/Kamadhenu-goushala/admin/settings.php#settings-group-payment" class="nav-link"><i class="fas fa-qrcode"></i> <span>Donate &amp; QR Settings</span></a>
            <a href="/Kamadhenu-goushala/admin/seva.php" class="nav-link <?php echo is_admin_active('seva.php', $current_page); ?>"><i class="fas fa-pray"></i> <span>Sacred Gouseva</span></a>
            
            <div class="nav-section-title">Media &amp; Communications</div>
            <a href="/Kamadhenu-goushala/admin/stories.php" class="nav-link <?php echo is_admin_active('stories.php', $current_page); ?>"><i class="fas fa-newspaper"></i> <span>News &amp; Updates</span></a>
            <a href="/Kamadhenu-goushala/admin/videos.php" class="nav-link <?php echo is_admin_active('videos.php', $current_page); ?>"><i class="fab fa-youtube"></i> <span>Program Videos</span></a>
            <a href="/Kamadhenu-goushala/admin/gallery.php" class="nav-link <?php echo is_admin_active('gallery.php', $current_page); ?>"><i class="fas fa-images"></i> <span>Photo Gallery</span></a>
            <a href="/Kamadhenu-goushala/admin/inventory.php" class="nav-link <?php echo is_admin_active('inventory.php', $current_page); ?>"><i class="fas fa-warehouse"></i> <span>Inventory Control</span></a>
            
            <div class="nav-section-title">Relief &amp; Events</div>
            <a href="/Kamadhenu-goushala/admin/emergency.php" class="nav-link <?php echo is_admin_active('emergency.php', $current_page); ?>"><i class="fas fa-ambulance"></i> <span>Emergency Relief</span></a>
            <a href="/Kamadhenu-goushala/admin/events.php" class="nav-link <?php echo is_admin_active('events.php', $current_page); ?>"><i class="fas fa-calendar-alt"></i> <span>Trust Events</span></a>
            <a href="/Kamadhenu-goushala/admin/products.php" class="nav-link <?php echo is_admin_active('products.php', $current_page); ?>"><i class="fas fa-store"></i> <span>Store Products</span></a>
            <a href="/Kamadhenu-goushala/admin/orders.php" class="nav-link <?php echo is_admin_active('orders.php', $current_page); ?>"><i class="fas fa-box-open"></i> <span>Customer Orders</span></a>
            <a href="/Kamadhenu-goushala/admin/volunteers.php" class="nav-link <?php echo is_admin_active('volunteers.php', $current_page); ?>"><i class="fas fa-hands-helping"></i> <span>Volunteers</span></a>
            
            <div class="nav-section-title">System &amp; Security</div>
            <a href="/Kamadhenu-goushala/admin/homepage-settings.php" class="nav-link <?php echo is_admin_active('homepage-settings.php', $current_page); ?>"><i class="fas fa-home"></i> <span>Homepage Manager</span></a>
            <a href="/Kamadhenu-goushala/admin/button-settings.php" class="nav-link <?php echo is_admin_active('button-settings.php', $current_page); ?>"><i class="fas fa-paint-brush text-warning"></i> <span>Button Customizer</span></a>
            <a href="/Kamadhenu-goushala/admin/about-settings.php" class="nav-link <?php echo is_admin_active('about-settings.php', $current_page); ?>"><i class="fas fa-info-circle"></i> <span>About Us Editor</span></a>
            <a href="/Kamadhenu-goushala/admin/whatsapp-numbers.php" class="nav-link <?php echo is_admin_active('whatsapp-numbers.php', $current_page); ?>"><i class="fab fa-whatsapp"></i> <span>WhatsApp Directory</span></a>
            <a href="/Kamadhenu-goushala/admin/reports.php" class="nav-link <?php echo is_admin_active('reports.php', $current_page); ?>"><i class="fas fa-file-invoice-dollar"></i> <span>Reports Export</span></a>
            <a href="/Kamadhenu-goushala/admin/audit-logs.php" class="nav-link <?php echo is_admin_active('audit-logs.php', $current_page); ?>"><i class="fas fa-history"></i> <span>Audit Logs</span></a>
            <a href="/Kamadhenu-goushala/admin/settings.php" class="nav-link <?php echo is_admin_active('settings.php', $current_page); ?>"><i class="fas fa-cog"></i> <span>Settings</span></a>
            
            <hr class="border-secondary border-opacity-25 my-3">
            <a href="/Kamadhenu-goushala/index.php" target="_blank" class="nav-link text-info"><i class="fas fa-external-link-alt"></i> <span>View Public Site</span></a>
            <a href="/Kamadhenu-goushala/admin/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout Admin</span></a>
        </div>
    </div>

    <div class="flex-grow-1 p-4 admin-main-content" style="min-width: 0;">
        <header class="d-flex align-items-center justify-content-between mb-4 admin-header-nav">
            <div class="d-flex align-items-center gap-2 me-auto">
                <h4 class="font-heading mb-0 text-white fs-5 fs-md-4"><i class="fas fa-shield-alt text-warning me-2 animate-pulse"></i> Management Console</h4>
                <span class="badge badge-admin-live-time px-3 py-2 rounded-pill font-mono d-none d-sm-inline-block"><i class="fas fa-clock me-1 text-cyan animate-spin-slow"></i> <span id="admin-live-time">--:--:--</span></span>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">
                <a href="/Kamadhenu-goushala/admin/cows.php" class="btn btn-sm btn-admin-header-search rounded-pill font-ui fw-bold px-3 d-none d-md-inline-block"><i class="fas fa-search me-1"></i> Search Database</a>
                <!-- Admin Dark Mode / Light Mode Switcher Toggle Button -->
                <button id="adminThemeToggleBtn" type="button" onclick="toggleAdminTheme()" class="btn btn-sm btn-outline-warning rounded-circle d-flex align-items-center justify-content-center p-2 shadow-sm" style="width: 38px; height: 38px;" title="Toggle Dark / Light Theme">
                    <i id="adminThemeIcon" class="fas fa-moon"></i>
                </button>
                <span class="badge badge-admin-role-glow font-ui fw-bold px-3 py-2 rounded-pill d-none d-md-inline-block"><i class="fas fa-user-shield me-1"></i> <?php echo e($admin['role_display']); ?></span>
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2 admin-profile-toggle cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <div class="rounded-circle text-white font-heading fw-bold d-flex align-items-center justify-content-center shadow-sm admin-profile-avatar" style="width: 38px; height: 38px;">
                            <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
                        </div>
                        <span class="font-ui fw-bold text-white d-none d-md-inline user-name-glow"><?php echo e($admin['name']); ?></span>
                        <i class="fas fa-chevron-down text-white-50 ms-1 small"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                        <li><h6 class="dropdown-header text-white-50 mb-0">Signed in as <br><strong class="text-white mt-1 d-block"><?php echo e($admin['name']); ?></strong></h6></li>
                        <li><hr class="dropdown-divider border-secondary border-opacity-25 my-2"></li>
                        <li><a class="dropdown-item" href="/Kamadhenu-goushala/admin/settings.php"><i class="fas fa-user-cog me-2 text-primary"></i> Account Settings</a></li>
                        <li><a class="dropdown-item" href="/Kamadhenu-goushala/index.php" target="_blank"><i class="fas fa-external-link-alt me-2 text-info"></i> View Public Site</a></li>
                        <li><hr class="dropdown-divider border-secondary border-opacity-25 my-2"></li>
                        <li><a class="dropdown-item text-danger" href="/Kamadhenu-goushala/admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout Admin</a></li>
                    </ul>
                </div>
                <!-- Mobile Hamburger Sidebar Toggle Button (Top Right) -->
                <button class="btn btn-outline-warning d-lg-none ms-1 px-3 py-2 rounded-3 shadow-sm" id="sidebar-toggle-btn" aria-label="Toggle Navigation" title="Toggle Navigation Menu">
                    <i class="fas fa-bars fs-5"></i>
                </button>
            </div>
        </header>

        <script>
        function applyAdminTheme(theme) {
            document.documentElement.setAttribute('data-admin-theme', theme);
            document.body.setAttribute('data-admin-theme', theme);
            const icon = document.getElementById('adminThemeIcon');
            if (icon) {
                if (theme === 'light') {
                    icon.className = 'fas fa-sun text-warning';
                } else {
                    icon.className = 'fas fa-moon text-warning';
                }
            }
        }

        function toggleAdminTheme() {
            const currentTheme = localStorage.getItem('kamadenu_admin_theme') || 'dark';
            const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
            localStorage.setItem('kamadenu_admin_theme', newTheme);
            applyAdminTheme(newTheme);
        }

        function updateAdminTime() {
            const el = document.getElementById('admin-live-time');
            if (el) {
                const now = new Date();
                el.textContent = now.toLocaleTimeString('en-US', { hour12: true });
            }
        }

        (function() {
            const savedTheme = localStorage.getItem('kamadenu_admin_theme') || 'dark';
            document.documentElement.setAttribute('data-admin-theme', savedTheme);
        })();

        document.addEventListener("DOMContentLoaded", function() {
            const savedTheme = localStorage.getItem('kamadenu_admin_theme') || 'dark';
            applyAdminTheme(savedTheme);
        });

        setInterval(updateAdminTime, 1000);
        updateAdminTime();
        </script>


