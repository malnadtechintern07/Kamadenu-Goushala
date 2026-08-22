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
    <title><?php echo __t('site_title'); ?> | <?php echo __t('tagline'); ?></title>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Theme & Master Stylesheets -->
    <link rel="stylesheet" href="/Kamadenu/css/style.css">
    <link id="theme-stylesheet" rel="stylesheet" href="/Kamadenu/css/<?php echo e($theme); ?>.css">
</head>

<body>

<!-- Devotional Top Announcement Bar -->
<div class="py-1 text-center bg-dark text-warning border-bottom border-warning">
    <small class="devotional-phrase"><i class="fas fa-om me-2"></i> “ಗೋ ಮಾತಾ ಕಿ ಜೈ” <i class="fas fa-om ms-2"></i></small>
</div>

<!-- Main Bootstrap 5 Compact Navigation -->
<nav class="navbar navbar-expand-xl navbar-dark navbar-kamadenu sticky-top py-2 shadow">
    <div class="container-fluid px-lg-3">
        <a class="navbar-brand py-0 me-3 d-flex align-items-center" href="/Kamadenu/index.php">
            <img src="/Kamadenu/assets/images/logo.png" alt="Kamadenu Goushala Trust Logo" class="brand-logo me-2.5" style="height: 44px; width: auto; object-fit: contain; filter: drop-shadow(0 3px 8px rgba(0,0,0,0.4));">

            <div class="d-inline-block align-middle">
                <span class="d-block lh-1 fs-5 fw-bold"><?php echo __t('site_title'); ?></span>
                <small class="fs-7 fw-normal text-warning d-none d-sm-block kn-text">ಗೋ ಮಾತಾ ಕಿ ಜೈ</small>
            </div>
        </a>

        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-xl-0 align-items-center gap-1">
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('index.php', $current_page); ?>" href="/Kamadenu/index.php"><?php echo __t('nav_home'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('about.php', $current_page); ?>" href="/Kamadenu/about.php"><?php echo __t('nav_about'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('cows.php', $current_page); ?>" href="/Kamadenu/cows.php"><?php echo __t('nav_cows'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('adopt.php', $current_page); ?>" href="/Kamadenu/adopt.php"><?php echo __t('nav_adopt'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('seva.php', $current_page); ?>" href="/Kamadenu/seva.php"><?php echo __t('nav_seva'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('products.php', $current_page); ?>" href="/Kamadenu/products.php"><?php echo __t('nav_products'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('events.php', $current_page); ?>" href="/Kamadenu/events.php"><?php echo __t('nav_events'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('emergency.php', $current_page); ?>" href="/Kamadenu/emergency.php"><?php echo __t('nav_emergency'); ?></a></li>
                <li class="nav-item"><a class="nav-link text-nowrap <?php echo is_nav_active('donate.php', $current_page); ?>" href="/Kamadenu/donate.php"><?php echo __t('nav_donate'); ?></a></li>

                <!-- Dropdown: Explore & Media -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap <?php echo is_dropdown_active(['stories.php', 'gallery.php', 'activity-feed.php'], $current_page); ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-compass me-1 text-warning"></i> Explore <i class="fas fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu shadow border-warning">
                        <li><a class="dropdown-item <?php echo is_nav_active('stories.php', $current_page); ?>" href="/Kamadenu/stories.php"><i class="fas fa-book-open me-2 text-warning"></i> <?php echo __t('nav_stories'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('gallery.php', $current_page); ?>" href="/Kamadenu/gallery.php"><i class="fas fa-images me-2 text-warning"></i> <?php echo __t('nav_gallery'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('activity-feed.php', $current_page); ?>" href="/Kamadenu/activity-feed.php"><i class="fas fa-stream me-2 text-warning"></i> <?php echo __t('nav_activity'); ?></a></li>
                    </ul>
                </li>

                <!-- Dropdown: Get Involved & Support -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap <?php echo is_dropdown_active(['volunteer.php', 'contact.php'], $current_page); ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-hands-helping me-1 text-warning"></i> Support <i class="fas fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu shadow border-warning">
                        <li><a class="dropdown-item <?php echo is_nav_active('volunteer.php', $current_page); ?>" href="/Kamadenu/volunteer.php"><i class="fas fa-user-plus me-2 text-warning"></i> <?php echo __t('nav_volunteer'); ?></a></li>
                        <li><a class="dropdown-item <?php echo is_nav_active('contact.php', $current_page); ?>" href="/Kamadenu/contact.php"><i class="fas fa-envelope me-2 text-warning"></i> <?php echo __t('nav_contact'); ?></a></li>
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

                <!-- Header Cart Link Button -->
                <a href="/Kamadenu/cart.php" class="btn btn-warning btn-sm rounded-pill font-ui fw-bold px-3 d-flex align-items-center gap-2 position-relative <?php echo is_nav_active('cart.php', $current_page); ?>" title="<?php echo __t('nav_cart'); ?>">
                    <i class="fas fa-shopping-basket"></i>
                    <span><?php echo __t('nav_cart'); ?></span>
                    <span class="badge rounded-pill bg-danger cart-badge" style="display:none;">0</span>
                </a>



                <?php if ($user): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm dropdown-toggle font-ui rounded-pill fw-semibold px-3" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo e($user['name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="/Kamadenu/dashboard.php"><i class="fas fa-tachometer-alt me-2 text-warning"></i> <?php echo __t('nav_dashboard'); ?></a></li>
                            <li><a class="dropdown-item" href="/Kamadenu/my-cows.php"><i class="fas fa-cow me-2 text-warning"></i> My Adopted Cows</a></li>
                            <li><a class="dropdown-item" href="/Kamadenu/my-donations.php"><i class="fas fa-hand-holding-heart me-2 text-warning"></i> My Donations</a></li>
                            <li><a class="dropdown-item" href="/Kamadenu/my-orders.php"><i class="fas fa-box me-2 text-warning"></i> My Orders</a></li>
                            <li><a class="dropdown-item" href="/Kamadenu/my-certificates.php"><i class="fas fa-certificate me-2 text-warning"></i> My Certificates</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/Kamadenu/api/auth.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i> <?php echo __t('nav_logout'); ?></a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/Kamadenu/login.php" class="btn btn-outline-light btn-sm rounded-pill font-ui px-3"><?php echo __t('nav_login'); ?></a>
                    <a href="/Kamadenu/register.php" class="btn btn-warning btn-sm rounded-pill font-ui px-3 fw-semibold"><?php echo __t('nav_register'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

