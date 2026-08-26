<!-- Floating UI Controls Panel -->
<div class="floating-control-panel">
    <!-- Quick Cart Button -->
    <div class="dropup">
        <a href="/Kamadenu/cart.php" class="floating-btn bg-warning text-dark position-relative shadow d-flex align-items-center justify-content-center text-decoration-none" title="<?php echo __t('nav_cart'); ?>">
            <i class="fas fa-shopping-basket"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" style="display:none;">0</span>
        </a>
    </div>

    <!-- Language Switcher Button & Dropdown -->
    <div class="dropup">
        <button type="button" class="floating-btn" data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo __t('lang_title'); ?>">
            <i class="fas fa-language"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end floating-dropdown-menu">
            <li><h6 class="dropdown-header font-ui text-uppercase fw-bold text-muted"><?php echo __t('lang_title'); ?></h6></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:void(0)" onclick="setLanguage('en')">English <?php echo $current_lang === 'en' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center kn-text" href="javascript:void(0)" onclick="setLanguage('kn')">ಕನ್ನಡ <?php echo $current_lang === 'kn' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:void(0)" onclick="setLanguage('hi')">ಹಿन्दी <?php echo $current_lang === 'hi' ? '<i class="fas fa-check text-success ms-2"></i>' : ''; ?></a></li>
        </ul>
    </div>

    <!-- Theme Switcher Button & Dropdown -->
    <div class="dropup">
        <button type="button" class="floating-btn" data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo __t('theme_title'); ?>">
            <i class="fas fa-palette"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end floating-dropdown-menu">
            <li><h6 class="dropdown-header font-ui text-uppercase fw-bold text-muted"><?php echo __t('theme_title'); ?></h6></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:void(0)" onclick="setTheme('terracotta')"><span class="badge bg-warning me-2">&nbsp;</span> <?php echo __t('theme_terracotta'); ?></a></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:void(0)" onclick="setTheme('light')"><span class="badge bg-light text-dark border me-2">&nbsp;</span> <?php echo __t('theme_light'); ?></a></li>
            <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:void(0)" onclick="setTheme('dark')"><span class="badge bg-dark me-2">&nbsp;</span> <?php echo __t('theme_dark'); ?></a></li>
        </ul>
    </div>
</div>
