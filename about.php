<?php
require_once __DIR__ . '/includes/header.php';

$lang = get_current_lang();
$about_title = get_setting($pdo, "about_title_" . $lang, __t('about_title'));
$about_subtitle = get_setting($pdo, "about_subtitle_" . $lang, __t('about_subtitle'));
$about_heritage_title = get_setting($pdo, "about_heritage_title_" . $lang, __t('about_heritage_title'));
$about_heritage_text1 = get_setting($pdo, "about_heritage_text1_" . $lang, __t('about_heritage_text1'));
$about_heritage_text2 = get_setting($pdo, "about_heritage_text2_" . $lang, __t('about_heritage_text2'));
$about_vows_title = get_setting($pdo, "about_vows_title_" . $lang, __t('about_vows_title'));

$about_heritage_image = get_setting($pdo, 'about_heritage_image', '');
$heritage_img_url = img_url(empty($about_heritage_image) ? 'assets/images/goushala-heritage.jpg' : $about_heritage_image);
?>

<!-- Header Banner -->
<section class="py-5 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark font-ui px-3 py-2 rounded-pill mb-2 fw-bold"><i class="fas fa-om me-1"></i> <?php echo __t('hero_badge'); ?></span>
                <h1 class="font-heading text-warning mb-1 display-5"><?php echo htmlspecialchars($about_title); ?></h1>
                <p class="lead text-white-50 mb-0"><?php echo htmlspecialchars($about_subtitle); ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<!-- Section 1: Goushala Heritage & History -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h6 class="text-warning text-uppercase font-ui fw-bold tracking-wider mb-2"><i class="fas fa-history me-1"></i> <?php echo __t('nav_about'); ?></h6>
                <h2 class="font-heading mb-4 display-6"><?php echo htmlspecialchars($about_heritage_title); ?></h2>
                <p class="lead text-secondary mb-3"><?php echo nl2br(htmlspecialchars($about_heritage_text1)); ?></p>
                <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($about_heritage_text2)); ?></p>
                
                <div class="row g-3 text-center font-ui fw-bold">
                    <div class="col-4">
                        <div class="p-3 bg-secondary-subtle rounded-3 border hover-glow">
                            <div class="fs-3 text-warning font-mono">100%</div>
                            <small class="text-dark d-block">Non-Killing Sanctuary</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-secondary-subtle rounded-3 border hover-glow">
                            <div class="fs-3 text-success font-mono">80G</div>
                            <small class="text-dark d-block">Tax Exemption Registered</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-secondary-subtle rounded-3 border hover-glow">
                            <div class="fs-3 text-danger font-mono">24/7</div>
                            <small class="text-dark d-block">Veterinary ICU Care</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heritage Photo with Glowing Cursor Hover Effect -->
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?php echo htmlspecialchars($heritage_img_url); ?>" alt="Kamadenu Goushala Trust Heritage" class="img-fluid rounded-4 shadow-lg border border-warning hover-glow w-100" style="max-height: 440px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-dark text-white rounded-3 bg-opacity-75 backdrop-blur font-ui border border-warning">
                        <small class="text-warning font-mono d-block">ESTABLISHED 2012</small>
                        <strong class="font-heading">Kamadenu Heritage Sanctuary, Bengaluru</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Pancha Vratas (Our 5 Sacred Vows) -->
<section class="py-5 bg-card border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning text-uppercase font-ui fw-bold"><i class="fas fa-hand-holding-heart me-1"></i> Core Principles</h6>
            <h2 class="font-heading display-6"><?php echo htmlspecialchars($about_vows_title); ?></h2>
            <p class="text-muted max-w-600 mx-auto">Guided by scriptural wisdom and practical action to protect Gomatha.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 hover-glow">
                    <div class="fs-1 text-warning mb-3"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="font-heading fs-4 mb-2"><?php echo __t('vow1_title'); ?></h3>
                    <p class="text-secondary small mb-0"><?php echo __t('vow1_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 hover-glow">
                    <div class="fs-1 text-danger mb-3"><i class="fas fa-stethoscope"></i></div>
                    <h3 class="font-heading fs-4 mb-2"><?php echo __t('vow2_title'); ?></h3>
                    <p class="text-secondary small mb-0"><?php echo __t('vow2_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 hover-glow">
                    <div class="fs-1 text-success mb-3"><i class="fas fa-leaf"></i></div>
                    <h3 class="font-heading fs-4 mb-2"><?php echo __t('vow3_title'); ?></h3>
                    <p class="text-secondary small mb-0"><?php echo __t('vow3_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 hover-glow">
                    <div class="fs-1 text-primary mb-3"><i class="fas fa-dna"></i></div>
                    <h3 class="font-heading fs-4 mb-2"><?php echo __t('vow4_title'); ?></h3>
                    <p class="text-secondary small mb-0"><?php echo __t('vow4_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 hover-glow">
                    <div class="fs-1 text-warning mb-3"><i class="fas fa-hands-praying"></i></div>
                    <h3 class="font-heading fs-4 mb-2"><?php echo __t('vow5_title'); ?></h3>
                    <p class="text-secondary small mb-0"><?php echo __t('vow5_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-4 h-100 bg-gradient-hero text-white hover-glow border-warning d-flex flex-column justify-content-center text-center">
                    <div class="devotional-phrase fs-2 mb-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                    <h4 class="font-heading text-warning mb-3">Join Our Sacred Mission</h4>
                    <a href="/Kamadenu/adopt.php" class="btn btn-warning rounded-pill font-ui fw-bold px-4 py-2"><?php echo __t('hero_btn_adopt'); ?> &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Section 3: Indigenous Breeds We Protect -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning text-uppercase font-ui fw-bold"><i class="fas fa-cow me-1"></i> Indigenous Cattle Wealth</h6>
            <h2 class="font-heading display-6">Pure Desi Breeds Protected at Kamadenu</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="kamadenu-card p-3 hover-glow">
                    <img src="<?php echo img_url('assets/images/cow-kamala.jpg'); ?>" class="img-fluid rounded mb-3 w-100" style="height: 200px; object-fit: cover;">
                    <h4 class="font-heading mb-1">Gir Breed (Gujarat)</h4>
                    <p class="small text-muted mb-0">Famous for its distinctive domed forehead, long pendulous ears, and high-quality A2 milk rich in beta-casein protein.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-3 hover-glow">
                    <img src="<?php echo img_url('assets/images/cow_sahiwal.jpg'); ?>" class="img-fluid rounded mb-3 w-100" style="height: 200px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=400&q=80'">
                    <h4 class="font-heading mb-1">Sahiwal Breed (Punjab)</h4>
                    <p class="small text-muted mb-0">Renowned for its reddish-brown coat, calm temperament, and exceptional heat tolerance in tropical Indian climates.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kamadenu-card p-3 hover-glow">
                    <img src="<?php echo img_url('assets/images/cow_kankrej.jpg'); ?>" class="img-fluid rounded mb-3 w-100" style="height: 200px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=400&q=80'">
                    <h4 class="font-heading mb-1">Kankrej Breed (Rajasthan)</h4>
                    <p class="small text-muted mb-0">Majestic dual-purpose breed with lyre-shaped horns, graceful Sawai gait, and legendary physical endurance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Organic Bio-Farming & Panchagavya -->
<section class="py-5 bg-card border-top border-bottom">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="<?php echo img_url('assets/images/event-workshop.jpg'); ?>" class="img-fluid rounded-4 shadow-lg border border-warning hover-glow w-100" style="max-height: 380px; object-fit: cover;">
            </div>
            <div class="col-lg-6">
                <h6 class="text-warning text-uppercase font-ui fw-bold"><i class="fas fa-seedling me-1"></i> Sustainable Bio-Farming</h6>
                <h2 class="font-heading mb-3">Vedic Panchagavya & Organic Agriculture</h2>
                <p class="text-secondary mb-3">At Kamadenu Goushala Trust, we believe in complete ecological harmony. Every gram of dung and urine from our indigenous cows is converted into organic bio-fertilizers like <strong>Jeevamrutha</strong>, <strong>Ghanajeevamrutha</strong>, and <strong>Panchagavya</strong>.</p>
                <p class="text-muted mb-4">We conduct free monthly workshops for local farmers, teaching them how to eliminate toxic chemical fertilizers, restore soil fertility, and achieve Zero-Budget Natural Farming using native cow outputs.</p>

                <a href="/Kamadenu/events.php" class="btn btn-kamadenu-primary font-ui fw-bold px-4 py-2">Browse Bio-Farming Events &rarr;</a>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Devotional Quote & CTA -->
<section class="py-5 bg-dark text-warning text-center border-top border-bottom">
    <div class="container">
        <i class="fas fa-om fs-1 text-warning mb-3 d-block"></i>
        <blockquote class="blockquote fs-3 font-heading text-light mb-3">
            "गावो विश्‍वस्‍य मातरः — The cow is indeed the mother of the universe."
        </blockquote>
        <div class="devotional-phrase fs-2 my-2">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
        <p class="text-white-50 font-ui mb-4">Rigveda & Vedic Scriptures</p>
        
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/Kamadenu/donate.php" class="btn btn-kamadenu-primary btn-lg font-ui fw-bold px-4 py-3"><i class="fas fa-heart me-2"></i> Make a Tax-Exempt Donation</a>
            <a href="/Kamadenu/adopt.php" class="btn btn-warning rounded-pill btn-lg font-ui fw-bold px-4 py-3 text-dark shadow"><i class="fas fa-cow me-2"></i> Adopt an Indigenous Cow</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
