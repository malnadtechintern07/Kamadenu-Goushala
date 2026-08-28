<?php
require_once __DIR__ . '/../config/database.php';

$theme = get_current_theme();
$lang = get_current_lang();
$user = current_user($pdo);
$current_page = basename($_SERVER['SCRIPT_NAME']);

function is_nav_active($page, $current) {
    return ($current === $page) ? 'active glow-nav-item fw-bold text-warning' : '';
}

function is_dropdown_active($pages, $current) {
    return in_array($current, $pages) ? 'active glow-nav-item fw-bold text-warning' : '';
}
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>" data-theme="<?php echo e($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(get_setting($pdo, 'site_name', 'Kamadenu Goushala Trust')); ?> | <?php echo __t('tagline'); ?></title>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Theme & Master Stylesheets -->
    <link rel="stylesheet" href="/Kamadhenu-goushala/css/style.css">
    <link id="theme-stylesheet" rel="stylesheet" href="/Kamadhenu-goushala/css/<?php echo e($theme); ?>.css">
    
    <!-- Favicon Icon -->
    <link rel="icon" type="image/svg+xml" href="/Kamadhenu-goushala/assets/images/favicon.svg">

    <script>
        window.isUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
    </script>

    <!-- Dynamic Custom Button Styles Configured from Admin Panel -->
    <?php
    $b_p_start = get_setting($pdo, 'btn_primary_bg_start', '#e67e22');
    $b_p_end = get_setting($pdo, 'btn_primary_bg_end', '#d35400');
    $b_p_text = get_setting($pdo, 'btn_primary_text_color', '#ffffff');
    $b_p_border = get_setting($pdo, 'btn_primary_border_color', '#ffd700');
    $b_p_radius = get_setting($pdo, 'btn_primary_border_radius', '50px');

    $b_f_start = get_setting($pdo, 'btn_feed_bg_start', '#10b981');
    $b_f_end = get_setting($pdo, 'btn_feed_bg_end', '#059669');
    $b_f_text = get_setting($pdo, 'btn_feed_text_color', '#ffffff');
    $b_f_border = get_setting($pdo, 'btn_feed_border_color', '#6ee7b7');
    $b_f_radius = get_setting($pdo, 'btn_feed_border_radius', '50px');

    $b_w_start = get_setting($pdo, 'btn_wa_bg_start', '#16a34a');
    $b_w_end = get_setting($pdo, 'btn_wa_bg_end', '#15803d');
    $b_w_text = get_setting($pdo, 'btn_wa_text_color', '#ffffff');
    $b_w_border = get_setting($pdo, 'btn_wa_border_color', '#86efac');
    $b_w_radius = get_setting($pdo, 'btn_wa_border_radius', '50px');

    $b_d_start = get_setting($pdo, 'btn_details_bg_start', '#1e293b');
    $b_d_end = get_setting($pdo, 'btn_details_bg_end', '#0f172a');
    $b_d_text = get_setting($pdo, 'btn_details_text_color', '#f8fafc');
    $b_d_border = get_setting($pdo, 'btn_details_border_color', '#38bdf8');
    $b_d_radius = get_setting($pdo, 'btn_details_border_radius', '50px');

    $b_c_start = get_setting($pdo, 'btn_cart_bg_start', '#fef3c7');
    $b_c_end = get_setting($pdo, 'btn_cart_bg_end', '#fde68a');
    $b_c_text = get_setting($pdo, 'btn_cart_text_color', '#b45309');
    $b_c_border = get_setting($pdo, 'btn_cart_border_color', '#f59e0b');
    $b_c_radius = get_setting($pdo, 'btn_cart_border_radius', '50px');
    ?>
    <style id="kamadenu-dynamic-admin-buttons">
    .btn-kamadenu-primary, .btn-sponsor-primary {
        background: linear-gradient(135deg, <?php echo e($b_p_start); ?> 0%, <?php echo e($b_p_end); ?> 100%) !important;
        color: <?php echo e($b_p_text); ?> !important;
        border: 1.5px solid <?php echo e($b_p_border); ?> !important;
        border-radius: <?php echo e($b_p_radius); ?> !important;
    }
    .btn-feed-cow {
        background: linear-gradient(135deg, <?php echo e($b_f_start); ?> 0%, <?php echo e($b_f_end); ?> 100%) !important;
        color: <?php echo e($b_f_text); ?> !important;
        border: 1.5px solid <?php echo e($b_f_border); ?> !important;
        border-radius: <?php echo e($b_f_radius); ?> !important;
    }
    .btn-cart, .btn-kamadenu-outline {
        background: linear-gradient(135deg, <?php echo e($b_c_start); ?> 0%, <?php echo e($b_c_end); ?> 100%) !important;
        color: <?php echo e($b_c_text); ?> !important;
        border: 1.5px solid <?php echo e($b_c_border); ?> !important;
        border-radius: <?php echo e($b_c_radius); ?> !important;
    }
    .btn-success, .btn-whatsapp {
        background: linear-gradient(135deg, <?php echo e($b_w_start); ?> 0%, <?php echo e($b_w_end); ?> 100%) !important;
        color: <?php echo e($b_w_text); ?> !important;
        border: 1.5px solid <?php echo e($b_w_border); ?> !important;
        border-radius: <?php echo e($b_w_radius); ?> !important;
    }
    .btn-cow-details {
        background: linear-gradient(135deg, <?php echo e($b_d_start); ?> 0%, <?php echo e($b_d_end); ?> 100%) !important;
        color: <?php echo e($b_d_text); ?> !important;
        border: 1.5px solid <?php echo e($b_d_border); ?> !important;
        border-radius: <?php echo e($b_d_radius); ?> !important;
    }
    </style>
</head>

<body>

<!-- Main Bootstrap 5 Compact Navigation -->
<nav class="navbar navbar-expand-xl navbar-dark navbar-kamadenu sticky-top py-2 shadow">
    <div class="container-fluid px-2 px-sm-3 px-lg-4 px-xl-5 align-items-center justify-content-between">
        <a class="navbar-brand py-0 me-2 d-flex align-items-center" href="/Kamadhenu-goushala/index.php" style="max-width: calc(100% - 65px);">
            <?php 
            $logo_setting = get_setting($pdo, 'website_logo', '');
            $logo_url = img_url(empty($logo_setting) ? 'assets/images/logo.png' : $logo_setting);
            ?>
            <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Kamadenu Goushala Trust Logo" class="brand-logo me-2" style="height: 42px; width: auto; object-fit: contain; filter: drop-shadow(0 3px 8px rgba(0,0,0,0.4)); flex-shrink: 0;">

            <div class="d-inline-block align-middle text-truncate">
                <span class="d-block lh-1 fs-5 fw-bold text-truncate"><?php echo e(get_setting($pdo, 'site_name', 'Kamadenu Goushala Trust')); ?></span>
                <small class="fs-7 fw-normal text-warning d-none d-sm-block kn-text text-truncate">ಗೋ ಮಾತಾ ಕಿ ಜೈ</small>
            </div>
        </a>

        <button class="navbar-toggler border-0 p-2 ms-auto text-warning shadow-none flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation" style="z-index: 1050;">
            <i class="fas fa-bars fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-xl-0 align-items-center gap-1">
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('index.php', $current_page); ?>" href="/Kamadhenu-goushala/index.php"><?php echo __t('nav_home'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('about.php', $current_page); ?>" href="/Kamadhenu-goushala/about.php"><?php echo __t('nav_about'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('cows.php', $current_page); ?>" href="/Kamadhenu-goushala/cows.php"><?php echo __t('nav_cows'); ?></a></li>
                
                <!-- Merged Dropdown: Adopt & Feed Cow -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap <?php echo is_dropdown_active(['adopt.php', 'feed-cow.php', 'feed.php'], $current_page); ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-heart text-warning me-1"></i> Adopt &amp; Feed Cow <i class="fas fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu shadow border-warning">
                        <li>
                            <a class="dropdown-item <?php echo is_nav_active('adopt.php', $current_page); ?>" href="/Kamadhenu-goushala/adopt.php">
                                <i class="fas fa-heart text-danger me-2"></i> Sponsor / Adopt Cow
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo is_nav_active('feed-cow.php', $current_page); ?>" href="/Kamadhenu-goushala/feed-cow.php">
                                <i class="fas fa-cookie-bite text-success me-2"></i> Feed Specific Cow
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo is_nav_active('feed.php', $current_page); ?>" href="/Kamadhenu-goushala/feed.php">
                                <i class="fas fa-wheat-awn text-warning me-2"></i> Fodder Seva (General)
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('seva.php', $current_page); ?>" href="/Kamadhenu-goushala/seva.php"><?php echo __t('nav_seva'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('products.php', $current_page); ?>" href="/Kamadhenu-goushala/products.php"><?php echo __t('nav_products'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('events.php', $current_page); ?>" href="/Kamadhenu-goushala/events.php"><?php echo __t('nav_events'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('emergency.php', $current_page); ?>" href="/Kamadhenu-goushala/emergency.php"><?php echo __t('nav_emergency'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('donate.php', $current_page); ?>" href="/Kamadhenu-goushala/donate.php"><?php echo __t('nav_donate'); ?></a></li>

                <!-- Dropdown: Explore & Media -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap <?php echo is_dropdown_active(['stories.php', 'gallery.php', 'activity-feed.php'], $current_page); ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Explore <i class="fas fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu shadow border-warning">
                        <li><a class="dropdown-item <?php echo is_nav_active('stories.php', $current_page); ?>" href="/Kamadhenu-goushala/stories.php"><?php echo __t('nav_stories'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('gallery.php', $current_page); ?>" href="/Kamadhenu-goushala/gallery.php"><?php echo __t('nav_gallery'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('activity-feed.php', $current_page); ?>" href="/Kamadhenu-goushala/activity-feed.php"><?php echo __t('nav_activity'); ?></a></li>
                    </ul>
                </li>

                <!-- Dropdown: Get Involved & Support -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap <?php echo is_dropdown_active(['volunteer.php', 'contact.php'], $current_page); ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Support <i class="fas fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu shadow border-warning">
                        <li><a class="dropdown-item <?php echo is_nav_active('volunteer.php', $current_page); ?>" href="/Kamadhenu-goushala/volunteer.php"><?php echo __t('nav_volunteer'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('contact.php', $current_page); ?>" href="/Kamadhenu-goushala/contact.php"><?php echo __t('nav_contact'); ?></a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">

                <!-- Header Language Switcher Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-warning btn-sm dropdown-toggle rounded-pill px-3 font-ui fw-bold d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                        <span class="text-uppercase"><?php echo e($lang); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-warning">
                        <li><h6 class="dropdown-header font-ui text-uppercase fw-bold text-muted"><?php echo __t('lang_title'); ?></h6></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $lang === 'en' ? 'fw-bold text-warning' : ''; ?>" href="javascript:void(0)" onclick="setLanguage('en')">English <?php echo $lang === 'en' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center kn-text <?php echo $lang === 'kn' ? 'fw-bold text-warning' : ''; ?>" href="javascript:void(0)" onclick="setLanguage('kn')">ಕನ್ನಡ <?php echo $lang === 'kn' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $lang === 'hi' ? 'fw-bold text-warning' : ''; ?>" href="javascript:void(0)" onclick="setLanguage('hi')">हिन्दी <?php echo $lang === 'hi' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
                    </ul>
                </div>

                <!-- Header User Login / Account Option -->
                <?php if ($user): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm dropdown-toggle font-ui rounded-pill fw-semibold px-3" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo e($user['name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="/Kamadhenu-goushala/dashboard.php"><i class="fas fa-tachometer-alt me-2 text-warning"></i> <?php echo __t('nav_dashboard'); ?></a></li>
                            <li><a class="dropdown-item" href="/Kamadhenu-goushala/my-cows.php"><i class="fas fa-cow me-2 text-warning"></i> My Adopted Cows</a></li>
                            <li><a class="dropdown-item" href="/Kamadhenu-goushala/my-donations.php"><i class="fas fa-hand-holding-heart me-2 text-warning"></i> My Donations</a></li>
                            <li><a class="dropdown-item" href="/Kamadhenu-goushala/my-orders.php"><i class="fas fa-box me-2 text-warning"></i> My Orders</a></li>
                            <li><a class="dropdown-item" href="/Kamadhenu-goushala/my-certificates.php"><i class="fas fa-certificate me-2 text-warning"></i> My Certificates</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/Kamadhenu-goushala/api/auth.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i> <?php echo __t('nav_logout'); ?></a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/Kamadhenu-goushala/login.php" class="btn btn-warning btn-sm rounded-pill font-ui fw-bold px-3 d-flex align-items-center gap-2 shadow-sm" title="<?php echo __t('nav_login'); ?>">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo __t('nav_login'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

