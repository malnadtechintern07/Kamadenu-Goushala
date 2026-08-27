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
$product_checkout_method = get_setting($pdo, 'product_checkout_method', 'both');

// Fetch Testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_featured = 1 ORDER BY id DESC")->fetchAll();

// Fetch Scripture Quote
$stmt = $pdo->query("SELECT * FROM quotes ORDER BY id DESC LIMIT 1");
$quote = $stmt->fetch();

// Retrieve active program videos from the database
$stmt_v = $pdo->query("SELECT * FROM videos ORDER BY id DESC LIMIT 3");
$db_videos = $stmt_v->fetchAll();

// Extract YouTube ID helper function
if (!function_exists('get_youtube_id')) {
    function get_youtube_id($url) {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }
        if (preg_match('%youtube\.com/shorts/([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }
        if (preg_match('%youtube\.com/embed/([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }
        return '';
    }
}

// Fallback default videos if none are configured in database yet
if (empty($db_videos)) {
    $db_videos = [
        [
            'title' => 'Daily Gouseva & Feeding Rituals',
            'description' => 'Experience the serene atmosphere during our morning grass feeding programs and daily Gouseva rituals performed with devotion.',
            'youtube_url' => 'https://www.youtube.com/watch?v=XmueYxEL6dg'
        ],
        [
            'title' => 'Cattle Rescue & Medical Rehabilitation',
            'description' => 'A glimpse into our rescue operations for stray, injured, and orphaned cows, and their medical recovery journey at our sanctuary hospital.',
            'youtube_url' => 'https://www.youtube.com/watch?v=a6n9Y69VPl0'
        ],
        [
            'title' => 'Devotees Cow Adoption Highlights',
            'description' => 'See the joy of families visiting their sponsored cows, participating in special sanctuary celebrations, and performing direct Gouseva.',
            'youtube_url' => 'https://www.youtube.com/watch?v=e_Kgr2Z5Grc'
        ]
    ];
}

// ── Self-heal homepage DB settings with defaults ─────────────────────────────
$hp_defaults_idx = [
    'hp_s1_bg'         => '/Kamadenu/assets/images/hero-bg.jpg',
    'hp_s1_overlay'    => 'rgba(20,10,5,0.82)',
    'hp_s1_badge'      => 'SACRED INDIGENOUS GOUSEVA SANCTUARY',
    'hp_s1_badge_color'=> 'warning',
    'hp_s1_badge_icon' => 'fas fa-om',
    'hp_s1_title'      => 'Kamadenu Goushala Trust — Sacred Shelter for Gou Mata',
    'hp_s1_subtitle'   => 'A Vedic sanctuary dedicated to the care, rescue, and spiritual wellbeing of indigenous Indian cattle. Your Gouseva brings merit, peace, and blessings to your family.',
    'hp_s1_phrase'     => '"ಗೋ ಮಾತಾ ಕಿ ಜೈ"',
    'hp_s1_btn1_text'  => 'Donate Now',
    'hp_s1_btn1_link'  => '/Kamadenu/donate.php',
    'hp_s1_btn1_class' => 'btn-kamadenu-primary',
    'hp_s1_btn2_text'  => 'Sponsor a Cow',
    'hp_s1_btn2_link'  => '/Kamadenu/adopt.php',
    'hp_s1_btn2_class' => 'btn-kamadenu-outline',
    'hp_s1_indicator'  => 'Sanctuary',
    'hp_s2_bg'         => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=1600&q=80',
    'hp_s2_overlay'    => 'rgba(10,20,15,0.82)',
    'hp_s2_badge'      => 'SPONSOR & ADOPT A COW',
    'hp_s2_badge_color'=> 'success',
    'hp_s2_badge_icon' => 'fas fa-hand-holding-heart',
    'hp_s2_title'      => 'Adopt a Sacred Mother Cow Today',
    'hp_s2_subtitle'   => 'Experience the joy of cow adoption. Sponsor the monthly feeds and medical care costs for a resident cow and get regular updates from the sanctuary.',
    'hp_s2_phrase'     => '"ಲೋಕಾಃ ಸಮಸ್ತಾಃ ಸುಖಿನೋ ಭವಂತು"',
    'hp_s2_btn1_text'  => 'Sponsor a Cow',
    'hp_s2_btn1_link'  => '/Kamadenu/adopt.php',
    'hp_s2_btn1_class' => 'btn-kamadenu-primary',
    'hp_s2_btn2_text'  => 'View Cow Passports',
    'hp_s2_btn2_link'  => '/Kamadenu/cows.php',
    'hp_s2_btn2_class' => 'btn-kamadenu-outline',
    'hp_s2_indicator'  => 'Adopt a Cow',
    'hp_s3_bg'         => 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=1600&q=80',
    'hp_s3_overlay'    => 'rgba(40,10,10,0.82)',
    'hp_s3_badge'      => 'EMERGENCY MEDICAL RELIEF',
    'hp_s3_badge_color'=> 'danger',
    'hp_s3_badge_icon' => 'fas fa-ambulance',
    'hp_s3_title'      => 'Rescue & Rehabilitate Street Cattle',
    'hp_s3_subtitle'   => 'Support our active emergency rescue campaigns. Your contributions provide shelter, critical surgery, and daily medication for injured and abandoned cows.',
    'hp_s3_phrase'     => '"ದಯವೇ ಧರ್ಮದ ಮೂಲವಯ್ಯಾ"',
    'hp_s3_btn1_text'  => 'Support Rescue Campaign',
    'hp_s3_btn1_link'  => '/Kamadenu/donate.php',
    'hp_s3_btn1_class' => 'btn btn-danger rounded-pill px-4 py-3 font-ui fw-bold shadow',
    'hp_s3_btn2_text'  => '',
    'hp_s3_btn2_link'  => '',
    'hp_s3_btn2_class' => '',
    'hp_s3_indicator'  => 'Rescue Relief',
    'hp_s4_bg'         => 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=1600&q=80',
    'hp_s4_overlay'    => 'rgba(25,20,10,0.82)',
    'hp_s4_badge'      => 'SACRED DAILY GOUSEVA',
    'hp_s4_badge_color'=> 'warning',
    'hp_s4_badge_icon' => 'fas fa-pray',
    'hp_s4_title'      => 'Perform Sacred Gouseva Offerings',
    'hp_s4_subtitle'   => 'Sponsor nutritional green feeds, morning prayers (Grasa Seva), and Vedic Gou Pooja rituals at the sanctuary. Bring blessings, peace, and spiritual prosperity to your home.',
    'hp_s4_phrase'     => '"ಗೌ ಪೂಜಾ ಮಹತ್ತ್ವಂ"',
    'hp_s4_btn1_text'  => 'Sponsor a Seva',
    'hp_s4_btn1_link'  => '/Kamadenu/seva.php',
    'hp_s4_btn1_class' => 'btn btn-warning rounded-pill px-4 py-3 font-ui fw-bold text-dark shadow',
    'hp_s4_btn2_text'  => '',
    'hp_s4_btn2_link'  => '',
    'hp_s4_btn2_class' => '',
    'hp_s4_indicator'  => 'Gouseva Rituals',
    'hp_cta_title'     => 'Join Our Sacred Gouseva Movement',
    'hp_cta_subtitle'  => 'Become a Gousevak — volunteer your time, skills, or resources to protect and nurture our indigenous cow heritage.',
    'hp_cta_btn_text'  => 'Apply as Volunteer',
    'hp_cta_btn_link'  => '/Kamadenu/volunteer.php',
    'hp_stats_title'   => 'Our Living Impact — Powered by Your Gouseva',
    'hp_stats_sub'     => 'REAL-TIME SANCTUARY METRICS',
];
ensure_hp_settings($pdo, $hp_defaults_idx);
?>

<!-- Hero Section with Background Slider & Touch Zones -->
<section class="hero-section" id="hero-section">
    <!-- Slide Background Layers Track -->
    <div class="hero-bg-slider-track">
        <?php for ($__s = 1; $__s <= 4; $__s++): ?>
        <div class="hero-slide-bg <?php echo $__s === 1 ? 'active' : ''; ?>" style="background-image: linear-gradient(135deg, <?php echo get_hp($pdo, "hp_s{$__s}_overlay", 'rgba(20,10,5,0.82)'); ?> 0%, <?php echo get_hp($pdo, "hp_s{$__s}_overlay", 'rgba(20,10,5,0.82)'); ?> 100%), url('<?php echo htmlspecialchars(img_url(get_hp($pdo, "hp_s{$__s}_bg", '')), ENT_QUOTES); ?>');"></div>
        <?php endfor; ?>
    </div>

    <!-- Touch Navigation Side Zones -->
    <div class="hero-nav-zone hero-nav-zone-left" onclick="prevHeroSlide()">
        <span class="hero-nav-arrow">&larr;</span>
    </div>
    <div class="hero-nav-zone hero-nav-zone-right" onclick="nextHeroSlide()">
        <span class="hero-nav-arrow">&rarr;</span>
    </div>

    <!-- Animated Golden Ambient Background Layers -->
    <div class="hero-background-aurora" style="z-index: 1;"></div>
    <canvas id="hero-particle-canvas" style="z-index: 1;"></canvas>

    <div class="container" style="z-index: 2;">
        <div class="hero-content-slider-wrapper">
            <div class="hero-content-slider-track">
                <!-- Dynamic Hero Slides (DB Controlled) -->
            <?php for ($__sl = 1; $__sl <= 4; $__sl++): ?>
            <?php
                $__s = "s{$__sl}";
                $__b1t = get_hp($pdo, "hp_{$__s}_btn1_text");
                $__b1l = get_hp($pdo, "hp_{$__s}_btn1_link");
                $__b1c = get_hp($pdo, "hp_{$__s}_btn1_class");
                $__b2t = get_hp($pdo, "hp_{$__s}_btn2_text");
                $__b2l = get_hp($pdo, "hp_{$__s}_btn2_link");
                $__b2c = get_hp($pdo, "hp_{$__s}_btn2_class");
                $__badge_color = get_hp($pdo, "hp_{$__s}_badge_color", 'warning');
                $__badge_icon  = get_hp($pdo, "hp_{$__s}_badge_icon",  'fas fa-om');
                $__is_warning  = in_array($__badge_color, ['warning']);
            ?>
            <div class="hero-slide-content <?php echo $__sl === 1 ? 'active' : ''; ?>">
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <div class="badge bg-<?php echo htmlspecialchars($__badge_color); ?> <?php echo $__is_warning ? 'text-dark' : 'text-white'; ?> font-ui px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm">
                            <i class="<?php echo htmlspecialchars($__badge_icon); ?> me-1"></i>
                            <?php echo htmlspecialchars(get_hp($pdo, "hp_{$__s}_badge")); ?>
                        </div>
                        <?php if ($__sl === 1): ?>
                        <h1 class="hero-title font-heading"><?php echo htmlspecialchars(get_hp($pdo, "hp_{$__s}_title")); ?></h1>
                        <?php else: ?>
                        <h2 class="hero-title font-heading"><?php echo htmlspecialchars(get_hp($pdo, "hp_{$__s}_title")); ?></h2>
                        <?php endif; ?>
                        <p class="hero-subtitle"><?php echo htmlspecialchars(get_hp($pdo, "hp_{$__s}_subtitle")); ?></p>
                        <div class="devotional-phrase mb-4 fs-2"><?php echo get_hp($pdo, "hp_{$__s}_phrase"); ?></div>
                        <div class="d-flex flex-wrap gap-3">
                            <?php if ($__b1t && $__b1l): ?>
                            <a href="<?php echo htmlspecialchars($__b1l); ?>" class="btn <?php echo htmlspecialchars($__b1c); ?> btn-lg">
                                <?php echo htmlspecialchars($__b1t); ?>
                            </a>
                            <?php endif; ?>
                            <?php if ($__b2t && $__b2l): ?>
                            <a href="<?php echo htmlspecialchars($__b2l); ?>" class="btn <?php echo htmlspecialchars($__b2c); ?> btn-lg">
                                <?php echo htmlspecialchars($__b2t); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($__sl === 1): ?>
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
                    <?php elseif ($__sl === 2): ?>
                    <div class="col-lg-5">
                        <div class="kamadenu-card p-4 text-dark position-relative shadow-lg bg-white border-success">
                            <div class="text-center mb-3">
                                <i class="fas fa-award text-success display-5 mb-2"></i>
                                <h4 class="font-heading text-dark">Sponsorship Perks</h4>
                                <p class="small text-muted">What you receive as a registered Gousevak</p>
                            </div>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="fas fa-certificate text-success me-2"></i> <strong>Official Adoption Certificate</strong></li>
                                <li class="mb-2"><i class="fas fa-camera text-success me-2"></i> <strong>Monthly Health &amp; Photo Updates</strong></li>
                                <li class="mb-2"><i class="fas fa-coins text-success me-2"></i> <strong>Gouseva Loyalty Points Awarded</strong></li>
                                <li class="mb-2"><i class="fas fa-pray text-success me-2"></i> <strong>Prayers Performed in Your Name</strong></li>
                            </ul>
                            <a href="/Kamadenu/cows.php" class="btn btn-success w-100 py-2 font-ui fw-bold"><i class="fas fa-cow me-2"></i> Find Cow to Adopt</a>
                        </div>
                    </div>
                    <?php elseif ($__sl === 3): ?>
                    <div class="col-lg-5">
                        <div class="kamadenu-card p-4 text-dark position-relative shadow-lg bg-white border-danger">
                            <span class="position-absolute top-0 end-0 m-3 badge bg-danger font-ui px-3 py-1 text-white shadow"><i class="fas fa-exclamation-circle me-1"></i> Critical Appeal</span>
                            <h4 class="font-heading mb-2 text-dark mt-2">Flood Rescue Campaign</h4>
                            <p class="small text-muted mb-3">Rescuing severely stranded and dehydrated cows from low-lying flooded areas near the riverbanks.</p>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small fw-bold mb-1"><span>Funding Progress</span><span class="text-danger">75% Raised</span></div>
                                <div class="progress" style="height: 10px;"><div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" style="width: 75%;"></div></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-2">
                                <div class="small"><div class="text-muted">Target Amount</div><strong class="font-mono">₹1,50,000</strong></div>
                                <div class="small text-end"><div class="text-muted">Raised</div><strong class="font-mono text-success">₹1,12,500</strong></div>
                            </div>
                            <a href="/Kamadenu/donate.php" class="btn btn-danger w-100 py-2 font-ui fw-bold"><i class="fas fa-heart me-2"></i> Donate to Relief</a>
                        </div>
                    </div>
                    <?php elseif ($__sl === 4): ?>
                    <div class="col-lg-5">
                        <div class="kamadenu-card p-4 text-dark position-relative shadow-lg bg-white border-warning">
                            <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark font-ui px-3 py-1 shadow"><i class="fas fa-om me-1"></i> Sacred Seva</span>
                            <h4 class="font-heading mb-2 text-dark mt-2">Gou Pooja &amp; Aarti Seva</h4>
                            <p class="small text-muted mb-3">Sponsor a traditional Vedic worship ceremony with flower garlands, Aaradhana, and special prayers performed in your name.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-3">
                                <div class="small"><div class="text-muted">Suggested Contribution</div><strong class="font-mono text-dark fs-5">₹1,500</strong></div>
                                <div class="small text-end text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                            <a href="/Kamadenu/seva-detail.php?id=2" class="btn btn-warning w-100 py-2 font-ui fw-bold text-dark"><i class="fas fa-pray me-2"></i> Book Seva Offering</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Slide Progress Indicators (Tabs — DB Controlled) -->
    <div class="hero-indicators-wrapper">
        <div class="container">
            <div class="hero-indicators-grid">
                <?php for ($__ind = 1; $__ind <= 4; $__ind++): ?>
                <div class="hero-indicator <?php echo $__ind === 1 ? 'active' : ''; ?>" onclick="showHeroSlide(<?php echo $__ind - 1; ?>)">
                    <div class="indicator-meta">
                        <span class="indicator-num"><?php echo str_pad($__ind, 2, '0', STR_PAD_LEFT); ?></span>
                        <span class="indicator-title"><?php echo htmlspecialchars(get_hp($pdo, "hp_s{$__ind}_indicator", "Slide {$__ind}")); ?></span>
                    </div>
                    <div class="indicator-progress-bg">
                        <div class="indicator-progress-fill"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- Recent Goushala Program Videos Section -->
<section class="py-5 text-white" id="recent-videos-section" style="background: linear-gradient(180deg, #0B0F19 0%, #111827 100%); position: relative; overflow: hidden;">
    <!-- Ambient decorative light glow -->
    <div style="position: absolute; width: 300px; height: 300px; background: rgba(245, 158, 11, 0.08); filter: blur(100px); top: -50px; left: -50px; pointer-events: none; border-radius: 50%;"></div>
    <div style="position: absolute; width: 350px; height: 350px; background: rgba(239, 68, 68, 0.06); filter: blur(120px); bottom: -50px; right: -50px; pointer-events: none; border-radius: 50%;"></div>

    <div class="container py-2">
        <div class="text-center mb-5">
            <span class="badge bg-danger text-white font-ui px-3.5 py-2 rounded-pill mb-2 fw-bold shadow" style="font-size: 0.75rem; letter-spacing: 0.08em; background: linear-gradient(135deg, #EF4444 0%, #B91C1C 100%) !important;">
                <i class="fab fa-youtube me-1.5 animate-pulse"></i> SACRED VIDEO GALLERY
            </span>
            <h2 class="font-heading display-6 mb-1 text-white fw-bold">Recent Goushala Programs &amp; Activities</h2>
            <div class="devotional-phrase text-warning my-2 font-heading fs-3" style="text-shadow: 0 0 15px rgba(245, 158, 11, 0.6); font-weight: bold; letter-spacing: 0.02em;">“ಗೋ ಮಾತಾ ಕಿ ಜೈ”</div>
            <p class="text-secondary max-w-600 mx-auto text-white-50 small font-ui">Watch videos of our recent daily Gouseva rituals, cow adoption updates, and emergency rescue campaigns at the sanctuary.</p>
        </div>

        <style>
        .premium-video-card {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(12px);
            height: 100%;
        }
        .premium-video-card:hover {
            transform: translateY(-8px);
            border-color: rgba(245, 158, 11, 0.45);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 25px rgba(245, 158, 11, 0.2);
        }
        .video-thumbnail-wrapper {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            margin: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            cursor: pointer;
        }
        .video-thumbnail-wrapper img {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-video-card:hover .video-thumbnail-wrapper img {
            transform: scale(1.08);
            opacity: 0.75;
        }
        .video-play-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }
        .premium-video-card:hover .video-play-overlay {
            background: rgba(15, 23, 42, 0.25);
        }
        .glowing-play-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .premium-video-card:hover .glowing-play-icon {
            transform: scale(1.15);
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.8);
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: #000;
        }
        .premium-video-card .card-body {
            padding: 0 1.5rem 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .premium-video-card .video-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #ffffff;
            transition: color 0.3s ease;
            line-height: 1.4;
        }
        .premium-video-card:hover .video-title {
            color: #F59E0B;
        }
        .premium-video-card .video-desc {
            font-size: 0.88rem;
            color: #94A3B8;
            line-height: 1.6;
            margin-bottom: 1.25rem;
            flex-grow: 1;
        }
        .premium-video-card .video-meta {
            font-size: 0.75rem;
            color: #64748B;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        </style>

        <div class="row g-4">
            <?php foreach ($db_videos as $vid): ?>
                <?php 
                    $video_id = get_youtube_id($vid['youtube_url']);
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="premium-video-card d-flex flex-column">
                        <?php if ($video_id): ?>
                            <a href="<?php echo htmlspecialchars($vid['youtube_url']); ?>" target="_blank" class="video-thumbnail-wrapper ratio ratio-16x9 d-block">
                                <img src="https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg" class="object-fit-cover w-100 h-100" alt="<?php echo e($vid['title']); ?>">
                                <div class="video-play-overlay">
                                    <div class="glowing-play-icon">
                                        <i class="fas fa-play ms-1"></i>
                                    </div>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="video-thumbnail-wrapper ratio ratio-16x9 bg-dark text-white d-flex align-items-center justify-content-center">
                                <span class="small text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Invalid Video URL</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="video-meta mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-video text-warning me-1"></i> Program Footage</span>
                                <span><i class="far fa-calendar-alt text-success me-1"></i> Gouseva Update</span>
                            </div>
                            <h4 class="video-title font-heading"><?php echo e($vid['title']); ?></h4>
                            <p class="video-desc"><?php echo e($vid['description']); ?></p>
                            
                            <?php if ($video_id): ?>
                                <a href="<?php echo htmlspecialchars($vid['youtube_url']); ?>" target="_blank" class="btn btn-sm btn-kamadenu-primary w-100 font-ui fw-bold mt-auto py-2 rounded-pill shadow-sm d-flex align-items-center justify-content-center">
                                    <i class="fab fa-youtube me-1.5"></i> Play on YouTube
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
                                <div class="d-flex flex-column gap-2 w-100">
                                    <?php 
                                        $p_wa = $pdo->prepare("SELECT wn.phone_number FROM products p JOIN whatsapp_numbers wn ON p.whatsapp_number_id = wn.id WHERE p.id = ?");
                                        $p_wa->execute([$p['id']]);
                                        $wa_phone_dir = $p_wa->fetchColumn();
                                        $wa_phone = !empty($wa_phone_dir) ? $wa_phone_dir : get_setting($pdo, 'whatsapp_order_default', '+91 98800 12345');
                                        $wa_msg = !empty($p['whatsapp_message']) ? $p['whatsapp_message'] : "Hare Krishna! I would like to purchase this product:\n- Product: " . $p['name'] . "\n- Price: ₹" . number_format($p['price'], 2) . "\n\nPlease let me know how to proceed.";
                                        $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $wa_phone) . "&text=" . urlencode($wa_msg);
                                        $p_checkout_method = !empty($p['contact_method']) ? $p['contact_method'] : $product_checkout_method;
                                    ?>
                                    <div class="d-flex gap-2 w-100">
                                        <button onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>')" class="btn btn-outline-dark btn-sm flex-fill font-ui fw-bold">
                                            <i class="fas fa-shopping-cart me-1"></i> <?php echo e(get_setting($pdo, 'btn_cart_label', __t('btn_add_to_cart'))); ?>
                                        </button>
                                        <button onclick="buyNow(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>', '<?php echo $p_checkout_method; ?>', '<?php echo addslashes($whatsapp_url); ?>')" class="btn btn-warning btn-sm flex-fill font-ui fw-bold text-dark shadow-sm">
                                            <i class="fas fa-bolt me-1"></i> <?php echo __t('btn_buy_now'); ?>
                                        </button>
                                    </div>
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






<!-- Volunteer CTA Banner — DB Controlled -->
<section class="py-5 bg-card border-top">
    <div class="container">
        <div class="kamadenu-card p-5 text-center bg-gradient-hero text-white border-warning">
            <h2 class="font-heading text-warning mb-2"><?php echo htmlspecialchars(get_hp($pdo, 'hp_cta_title', __t('volunteer_cta_title'))); ?></h2>
            <p class="lead text-white-50 mb-4 max-w-600 mx-auto"><?php echo htmlspecialchars(get_hp($pdo, 'hp_cta_subtitle', __t('volunteer_cta_desc'))); ?></p>
            <a href="<?php echo htmlspecialchars(get_hp($pdo, 'hp_cta_btn_link', '/Kamadenu/volunteer.php')); ?>" class="btn btn-kamadenu-primary btn-lg font-ui fw-bold px-5 shadow">
                <i class="fas fa-hand-holding-heart me-2"></i>
                <?php echo htmlspecialchars(get_hp($pdo, 'hp_cta_btn_text', __t('apply_volunteer_btn'))); ?>
            </a>
        </div>
    </div>
</section>


<script>
let currentHeroSlideIdx = 0;
const heroBgs = document.querySelectorAll('.hero-slide-bg');
const heroContents = document.querySelectorAll('.hero-slide-content');
const heroIndicators = document.querySelectorAll('.hero-indicator');
const bgTrack = document.querySelector('.hero-bg-slider-track');
const contentTrack = document.querySelector('.hero-content-slider-track');
const totalHeroSlides = heroBgs.length;
let heroAutoplayTimer;

function showHeroSlide(index) {
    if (index >= totalHeroSlides) index = 0;
    if (index < 0) index = totalHeroSlides - 1;
    
    currentHeroSlideIdx = index;
    
    heroBgs.forEach(bg => bg.classList.remove('active'));
    heroContents.forEach(c => c.classList.remove('active'));
    heroIndicators.forEach(ind => ind.classList.remove('active'));
    
    heroBgs[currentHeroSlideIdx].classList.add('active');
    heroContents[currentHeroSlideIdx].classList.add('active');
    if (heroIndicators[currentHeroSlideIdx]) {
        heroIndicators[currentHeroSlideIdx].classList.add('active');
    }

    const percentage = currentHeroSlideIdx * (100 / totalHeroSlides);
    if (bgTrack) {
        bgTrack.style.transform = `translateX(-${percentage}%)`;
    }
    if (contentTrack) {
        contentTrack.style.transform = `translateX(-${percentage}%)`;
    }
    
    resetHeroAutoplay();
}

function nextHeroSlide() {
    showHeroSlide(currentHeroSlideIdx + 1);
}

function prevHeroSlide() {
    showHeroSlide(currentHeroSlideIdx - 1);
}

function resetHeroAutoplay() {
    clearInterval(heroAutoplayTimer);
    heroAutoplayTimer = setInterval(nextHeroSlide, 7000); // Cycle slides every 7 seconds
}

resetHeroAutoplay();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
