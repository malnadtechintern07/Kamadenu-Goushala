<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Core Dynamic Metrics from MySQL
$total_cows = $pdo->query("SELECT COUNT(*) FROM cows")->fetchColumn();
$active_sponsors = $pdo->query("SELECT COUNT(*) FROM sponsorships WHERE status = 'Active'")->fetchColumn();
$total_donations_val = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status = 'Completed'")->fetchColumn();
$total_volunteers = $pdo->query("SELECT COUNT(*) FROM volunteers WHERE status = 'Approved'")->fetchColumn();

// Fetch Featured Cows
$stmt = $pdo->query("SELECT * FROM cows WHERE is_featured = 1 OR adoption_status = 'Available' ORDER BY id ASC LIMIT 3");
$featured_cows = $stmt->fetchAll();

// Fetch Emergency Relief Campaign
$stmt = $pdo->query("SELECT * FROM emergency_campaigns WHERE status = 'Active' ORDER BY id DESC LIMIT 1");
$emergency = $stmt->fetch();

// Fetch Seva Items
$stmt = $pdo->query("SELECT * FROM seva WHERE is_active = 1 LIMIT 4");
$seva_list = $stmt->fetchAll();

// Fetch Featured A2 Products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id ASC LIMIT 3");
$featured_products = $stmt->fetchAll();

// Fetch Testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_featured = 1 ORDER BY id DESC")->fetchAll();

// Fetch Scripture Quote
$stmt = $pdo->query("SELECT * FROM quotes ORDER BY id DESC LIMIT 1");
$quote = $stmt->fetch();
?>

<!-- Hero Section with Background Image & 3D Sacred Aarti Lamp Overlay -->
<section class="hero-section" id="hero-section">
    <!-- Animated Golden Ambient Background Layers -->
    <div class="hero-background-aurora"></div>
    <canvas id="hero-particle-canvas"></canvas>




    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="badge bg-warning text-dark font-ui px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm">
                    <i class="fas fa-om me-1"></i> SACRED INDIGENOUS GOUSEVA SANCTUARY
                </div>
                <h1 class="hero-title font-heading">
                    <?php echo __t('hero_title'); ?>
                </h1>
                <p class="hero-subtitle">
                    <?php echo __t('hero_subtitle'); ?>
                </p>

                <div class="devotional-phrase mb-4 fs-2">
                    “ಗೋ ಮಾತಾ ಕಿ ಜೈ”
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <a href="/Kamadenu/donate.php" class="btn btn-kamadenu-primary btn-lg">
                        <i class="fas fa-heart me-2"></i> <?php echo __t('hero_btn_donate'); ?>
                    </a>
                    <a href="/Kamadenu/adopt.php" class="btn btn-kamadenu-outline btn-lg">
                        <i class="fas fa-hand-holding-heart me-2"></i> <?php echo __t('hero_btn_adopt'); ?>
                    </a>
                    <a href="/Kamadenu/seva.php" class="btn btn-warning rounded-pill px-4 py-3 font-ui fw-bold text-dark shadow">
                        <i class="fas fa-pray me-2"></i> <?php echo __t('hero_btn_seva'); ?>
                    </a>
                </div>
            </div>

            <!-- Rescued Cow Kamala Featured Card -->
            <div class="col-lg-5">
                <div class="hero-cow-kamala-card text-dark position-relative shadow-lg">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-danger font-ui px-3 py-2 shadow"><i class="fas fa-heart me-1"></i> <?php echo __t('sponsor_kamala'); ?></span>
                    <img src="/Kamadenu/assets/images/cow-kamala.jpg" alt="Rescued Cow Kamala" class="hero-cow-kamala-img mb-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="font-heading mb-0 text-dark">Rescued Cow "Kamala" <small class="text-warning font-ui">(KG-001)</small></h4>
                        <span class="badge bg-warning-subtle text-dark border border-warning">Gir Breed</span>
                    </div>
                    <p class="small text-muted mb-3"><i class="fas fa-shield-alt text-success me-1"></i> <?php echo __t('kamala_desc'); ?></p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                        <span class="font-mono fw-bold text-dark fs-5">₹2,500 <small class="fs-6 text-muted font-ui">/ <?php echo __t('monthly_care'); ?></small></span>
                        <a href="/Kamadenu/adopt.php?cow_id=1" class="btn btn-kamadenu-primary btn-sm px-4 font-ui fw-bold"><i class="fas fa-hand-holding-heart me-1"></i> <?php echo __t('sponsor_kamala'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Impact Statistics Counter Bar -->
<section class="py-5 bg-card border-bottom shadow-sm">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3">
                <div class="p-3 border-end">
                    <i class="fas fa-cow text-warning fs-2 mb-2 d-block"></i>
                    <div class="fs-1 fw-bold text-dark font-mono"><?php echo $total_cows; ?>+</div>
                    <div class="text-uppercase font-ui small fw-bold text-muted"><?php echo __t('stat_cows'); ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-end-md">
                    <i class="fas fa-users text-success fs-2 mb-2 d-block"></i>
                    <div class="fs-1 fw-bold text-dark font-mono"><?php echo $active_sponsors; ?>+</div>
                    <div class="text-uppercase font-ui small fw-bold text-muted"><?php echo __t('stat_sponsors'); ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-end">
                    <i class="fas fa-hand-holding-heart text-danger fs-2 mb-2 d-block"></i>
                    <div class="fs-1 fw-bold text-dark font-mono live-total-donations">₹<?php echo number_format($total_donations_val); ?></div>
                    <div class="text-uppercase font-ui small fw-bold text-muted"><?php echo __t('stat_donations'); ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <i class="fas fa-hands-helping text-primary fs-2 mb-2 d-block"></i>
                    <div class="fs-1 fw-bold text-dark font-mono"><?php echo $total_volunteers + 24; ?>+</div>
                    <div class="text-uppercase font-ui small fw-bold text-muted"><?php echo __t('stat_volunteers'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Indigenous Cows -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-warning text-uppercase font-ui fw-bold tracking-wider mb-1"><i class="fas fa-shield-alt me-1"></i> <?php echo __t('home_cows_subtitle'); ?></h6>
                <h2 class="font-heading mb-0"><?php echo __t('home_cows_title'); ?></h2>
            </div>
            <a href="/Kamadenu/cows.php" class="btn btn-outline-warning rounded-pill px-4 font-ui fw-semibold mt-3 mt-md-0"><?php echo __t('browse_cows_btn'); ?> <i class="fas fa-arrow-right ms-2"></i></a>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_cows as $cow): ?>
                <?php 
                    $cname = __td($cow, 'name');
                    $cstory = __td($cow, 'rescue_story');
                ?>
                <div class="col-md-4">
                    <div class="kamadenu-card h-100">
                        <div class="position-relative overflow-hidden">
                            <img src="/Kamadenu/<?php echo e($cow['photo']); ?>" alt="<?php echo e($cname); ?>" class="cow-card-img" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
                            <span class="position-absolute top-0 end-0 m-3 badge-cow-code"><?php echo e($cow['cow_code']); ?></span>
                            <span class="position-absolute top-0 start-0 m-3 badge <?php echo $cow['adoption_status'] === 'Sponsored' ? 'bg-secondary' : 'bg-success'; ?> px-3 py-2 font-ui">
                                <?php echo $cow['adoption_status'] === 'Sponsored' ? __t('status_sponsored') : __t('status_available'); ?>
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="font-heading fs-4 mb-0"><?php echo e($cname); ?></h3>
                                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1"><?php echo e($cow['breed']); ?></span>
                                </div>
                                <p class="text-muted small mb-3"><?php echo e(mb_strimwidth($cstory, 0, 110, '...')); ?></p>

                                <div class="row g-2 small text-muted bg-secondary-subtle p-2.5 rounded mb-3">
                                    <div class="col-6"><strong><?php echo __t('cow_age'); ?>:</strong> <?php echo $cow['age_years']; ?> Yrs</div>
                                    <div class="col-6"><strong><?php echo __t('cow_health'); ?>:</strong> <span class="text-success fw-bold"><?php echo e($cow['health_status']); ?></span></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                                <div>
                                    <small class="text-muted d-block font-ui"><?php echo __t('cow_monthly_cost'); ?></small>
                                    <span class="fs-5 fw-bold text-dark font-mono">₹<?php echo number_format($cow['monthly_sponsorship_amount']); ?></span>
                                </div>
                                <a href="/Kamadenu/cow-detail.php?id=<?php echo $cow['id']; ?>" class="btn btn-kamadenu-primary btn-sm px-3 font-ui fw-bold"><?php echo __t('cow_btn_view'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Emergency Relief Campaign Banner -->
<?php if ($emergency): ?>
<section class="py-5 bg-warning-subtle border-top border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-danger font-ui text-uppercase px-3 py-2 rounded-pill mb-2"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo __t('emergency_badge'); ?></span>
                <h2 class="font-heading text-danger mb-3"><?php echo e(__td($emergency, 'title')); ?></h2>
                <p class="lead text-dark mb-4"><?php echo e(__td($emergency, 'story')); ?></p>

                <div class="kamadenu-card p-3 mb-4 bg-white border-danger">
                    <div class="d-flex justify-content-between font-ui fw-bold mb-1">
                        <span><?php echo __t('funding_target'); ?>: ₹<?php echo number_format($emergency['target_amount']); ?></span>
                        <span><?php echo __t('amount_raised'); ?>: <span id="campaign-raised-<?php echo $emergency['id']; ?>" class="text-success">₹<?php echo number_format($emergency['raised_amount']); ?></span></span>
                    </div>
                    <?php $pct = min(100, round(($emergency['raised_amount'] / $emergency['target_amount']) * 100)); ?>
                    <div class="progress" style="height: 22px;">
                        <div id="campaign-progress-<?php echo $emergency['id']; ?>" class="progress-bar bg-success progress-bar-striped progress-bar-animated font-mono fw-bold" style="width: <?php echo $pct; ?>%;"><?php echo $pct; ?>%</div>
                    </div>
                </div>

                <a href="/Kamadenu/donate.php?campaign=<?php echo $emergency['id']; ?>" class="btn btn-danger btn-lg rounded-pill font-ui fw-bold px-4 shadow"><i class="fas fa-hand-holding-heart me-2"></i> <?php echo __t('emergency_donate_now'); ?></a>
            </div>
            <div class="col-lg-5 text-center">
                <img src="/Kamadenu/<?php echo e($emergency['photo']); ?>" alt="Emergency Rescue" class="img-fluid rounded-4 shadow-lg border border-warning w-100" style="max-height: 340px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=600&q=80'">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Daily Seva Activities Grid -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning text-uppercase font-ui fw-bold tracking-wider mb-1"><i class="fas fa-hands-praying me-1"></i> <?php echo __t('nav_seva'); ?></h6>
            <h2 class="font-heading"><?php echo __t('seva_heading'); ?></h2>
            <p class="text-muted max-w-600 mx-auto"><?php echo __t('seva_subheading'); ?></p>
        </div>

        <div class="row g-4">
            <?php foreach ($seva_list as $s): ?>
                <?php 
                    $stitle = __td($s, 'title');
                    $sdesc = __td($s, 'description');
                ?>
                <div class="col-md-6 col-lg-3">
                    <div class="kamadenu-card text-center p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-1 text-warning mb-3">
                                <i class="fas <?php echo e($s['icon']); ?>"></i>
                            </div>
                            <h3 class="font-heading fs-5 mb-1"><?php echo e($stitle); ?></h3>
                            <p class="small text-muted mb-3"><?php echo e($sdesc); ?></p>
                        </div>

                        <div>
                            <div class="fs-4 fw-bold text-dark font-mono mb-3">₹<?php echo number_format($s['suggested_amount']); ?></div>
                            <a href="/Kamadenu/seva-detail.php?id=<?php echo $s['id']; ?>" class="btn btn-outline-warning rounded-pill btn-sm w-100 font-ui fw-semibold"><?php echo __t('seva_sponsor_btn'); ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Sacred A2 Products Store Preview -->
<section class="py-5 bg-card border-top border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-warning text-uppercase font-ui fw-bold"><i class="fas fa-store me-1"></i> <?php echo __t('nav_products'); ?></h6>
                <h2 class="font-heading mb-0"><?php echo __t('products_title'); ?></h2>
            </div>
            <a href="/Kamadenu/products.php" class="btn btn-outline-warning rounded-pill font-ui fw-semibold mt-2 mt-md-0"><?php echo __t('explore_store'); ?> <i class="fas fa-store ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_products as $p): ?>
                <?php 
                    $fpname = __td($p, 'name');
                    $fpdesc = __td($p, 'description');
                ?>
                <div class="col-md-4">
                    <div class="kamadenu-card h-100">
                        <img src="/Kamadenu/<?php echo e($p['image']); ?>" class="cow-card-img" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'" alt="<?php echo e($fpname); ?>">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-warning-subtle text-dark border border-warning mb-2"><?php echo e($p['category_name']); ?></span>
                                <h3 class="font-heading fs-5 mb-1"><?php echo e($fpname); ?></h3>
                                <p class="small text-muted mb-3"><?php echo e(mb_strimwidth($fpdesc, 0, 90, '...')); ?></p>
                            </div>

                            <div class="pt-3 border-top mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-4 fw-bold text-dark font-mono">₹<?php echo number_format($p['price']); ?></span>
                                    <small class="text-muted font-ui"><?php echo __t('product_stock'); ?>: <?php echo $p['stock_quantity']; ?></small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>')" class="btn btn-outline-dark btn-sm flex-fill font-ui fw-bold"><i class="fas fa-shopping-cart me-1"></i> <?php echo __t('btn_add_to_cart'); ?></button>
                                    <button onclick="buyNow(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>')" class="btn btn-warning btn-sm flex-fill font-ui fw-bold text-dark shadow-sm"><i class="fas fa-bolt me-1"></i> <?php echo __t('btn_buy_now'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Devotional Testimonials Carousel -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning text-uppercase font-ui fw-bold"><i class="fas fa-quote-left me-1"></i> <?php echo __t('devotee_sub'); ?></h6>
            <h2 class="font-heading"><?php echo __t('devotee_voices'); ?></h2>
        </div>

        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6">
                    <div class="kamadenu-card p-4 h-100">
                        <div class="text-warning mb-2">
                            <?php for ($i = 0; $i < $t['rating']; $i++): ?><i class="fas fa-star me-1"></i><?php endfor; ?>
                        </div>
                        <p class="fst-italic text-secondary mb-3">"<?php echo e($t['quote']); ?>"</p>
                        <div class="d-flex align-items-center gap-3 border-top pt-3">
                            <div class="rounded-circle bg-warning text-dark font-heading fw-bold p-2 px-3"><?php echo substr($t['name'], 0, 1); ?></div>
                            <div>
                                <strong class="font-heading d-block text-dark mb-0"><?php echo e($t['name']); ?></strong>
                                <small class="text-muted font-ui"><?php echo e($t['role']); ?> &bull; <?php echo e($t['location']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Devotional Scripture Quote Banner -->
<?php if ($quote): ?>
<section class="py-5 bg-dark text-warning text-center border-top border-bottom border-warning">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <i class="fas fa-om fs-1 text-warning mb-3 d-block"></i>
                <blockquote class="blockquote fs-3 font-heading text-light mb-3">
                    <?php 
                    if ($current_lang === 'kn') echo e($quote['quote_kn']);
                    elseif ($current_lang === 'hi') echo e($quote['quote_hi']);
                    else echo e($quote['quote_en']);
                    ?>
                </blockquote>
                <div class="devotional-phrase fs-4 my-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                <figcaption class="blockquote-footer text-warning font-ui mt-2">
                    <cite title="Source"><?php echo e($quote['source']); ?></cite>
                </figcaption>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning text-uppercase font-ui fw-bold"><?php echo __t('faq_sub'); ?></h6>
            <h2 class="font-heading"><?php echo __t('faq_title'); ?></h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item kamadenu-card mb-3 border">
                        <h2 class="accordion-header">
                            <button class="accordion-button font-heading fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <?php echo __t('faq1_q'); ?>
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                <?php echo __t('faq1_a'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item kamadenu-card mb-3 border">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-heading fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <?php echo __t('faq2_q'); ?>
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                <?php echo __t('faq2_a'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item kamadenu-card mb-3 border">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-heading fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <?php echo __t('faq3_q'); ?>
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                <?php echo __t('faq3_a'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Goushala Program Videos Section -->
<section class="py-5 bg-card border-top" id="recent-videos-section">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark font-ui px-3 py-2 rounded-pill mb-2 fw-bold shadow-sm">
                <i class="fab fa-youtube me-1"></i> SACRED MOMENTS
            </span>
            <h2 class="font-heading display-6 mb-2">Recent Goushala Programs & Activities</h2>
            <p class="text-secondary max-w-600 mx-auto">Watch videos of our recent daily Gouseva rituals, cow adoption updates, and emergency rescue campaigns at the sanctuary.</p>
        </div>

        <div class="row g-4">
            <!-- Video 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="kamadenu-card h-100 p-3 shadow-sm border border-warning-subtle d-flex flex-column">
                    <div class="ratio ratio-16x9 mb-3 rounded overflow-hidden shadow-sm">
                        <iframe src="https://www.youtube.com/embed/XmueYxEL6dg" title="Goushala Daily Seva & Feeding" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen style="border: 0;"></iframe>
                    </div>
                    <h5 class="font-heading mb-2">Daily Gouseva & Feeding Rituals</h5>
                    <p class="small text-muted flex-grow-1">Experience the serene atmosphere during our morning grass feeding programs and daily Gouseva rituals performed with devotion.</p>
                    <a href="https://www.youtube.com/watch?v=XmueYxEL6dg" target="_blank" class="btn btn-sm btn-outline-warning w-100 font-ui fw-bold mt-auto">
                        <i class="fab fa-youtube me-1.5"></i> Watch on YouTube
                    </a>
                </div>
            </div>

            <!-- Video 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="kamadenu-card h-100 p-3 shadow-sm border border-warning-subtle d-flex flex-column">
                    <div class="ratio ratio-16x9 mb-3 rounded overflow-hidden shadow-sm">
                        <iframe src="https://www.youtube.com/embed/a6n9Y69VPl0" title="Sanctuary Rescue & Medical Care Operations" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen style="border: 0;"></iframe>
                    </div>
                    <h5 class="font-heading mb-2">Cattle Rescue & Medical Rehabilitation</h5>
                    <p class="small text-muted flex-grow-1">A glimpse into our rescue operations for stray, injured, and orphaned cows, and their medical recovery journey at our sanctuary hospital.</p>
                    <a href="https://www.youtube.com/watch?v=a6n9Y69VPl0" target="_blank" class="btn btn-sm btn-outline-warning w-100 font-ui fw-bold mt-auto">
                        <i class="fab fa-youtube me-1.5"></i> Watch on YouTube
                    </a>
                </div>
            </div>

            <!-- Video 3 -->
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="kamadenu-card h-100 p-3 shadow-sm border border-warning-subtle d-flex flex-column">
                    <div class="ratio ratio-16x9 mb-3 rounded overflow-hidden shadow-sm">
                        <iframe src="https://www.youtube.com/embed/e_Kgr2Z5Grc" title="Adopted Cow Updates & Devotees Celebrations" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen style="border: 0;"></iframe>
                    </div>
                    <h5 class="font-heading mb-2">Devotees Cow Adoption Highlights</h5>
                    <p class="small text-muted flex-grow-1">See the joy of families visiting their sponsored cows, participating in special sanctuary celebrations, and performing direct Gouseva.</p>
                    <a href="https://www.youtube.com/watch?v=e_Kgr2Z5Grc" target="_blank" class="btn btn-sm btn-outline-warning w-100 font-ui fw-bold mt-auto">
                        <i class="fab fa-youtube me-1.5"></i> Watch on YouTube
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Selfless Volunteer CTA Banner -->
<section class="py-5 bg-card border-top">
    <div class="container">
        <div class="kamadenu-card p-5 text-center bg-gradient-hero text-white border-warning">
            <h2 class="font-heading text-warning mb-2"><?php echo __t('volunteer_cta_title'); ?></h2>
            <p class="lead text-white-50 mb-4 max-w-600 mx-auto"><?php echo __t('volunteer_cta_desc'); ?></p>
            <a href="/Kamadenu/volunteer.php" class="btn btn-kamadenu-primary btn-lg font-ui fw-bold px-5 shadow"><i class="fas fa-hand-holding-heart me-2"></i> <?php echo __t('apply_volunteer_btn'); ?></a>
        </div>
    </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
