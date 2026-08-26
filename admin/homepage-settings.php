<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

// ─── Default Homepage Settings (Self-Healing Bootstrap) ─────────────────────
$hp_defaults = [
    // Hero Slide 1
    'hp_s1_bg'          => '/Kamadenu/assets/images/hero-bg.jpg',
    'hp_s1_overlay'     => 'rgba(20,10,5,0.82)',
    'hp_s1_badge'       => 'SACRED INDIGENOUS GOUSEVA SANCTUARY',
    'hp_s1_badge_color' => 'warning',
    'hp_s1_badge_icon'  => 'fas fa-om',
    'hp_s1_title'       => 'Kamadenu Goushala — Sacred Shelter for Gou Mata',
    'hp_s1_subtitle'    => 'A Vedic sanctuary dedicated to the care, rescue, and spiritual wellbeing of indigenous Indian cattle. Your Gouseva brings merit, peace, and blessings to your family.',
    'hp_s1_phrase'      => '"ಗೋ ಮಾತಾ ಕಿ ಜೈ"',
    'hp_s1_btn1_text'   => 'Donate Now',
    'hp_s1_btn1_link'   => '/Kamadenu/donate.php',
    'hp_s1_btn1_class'  => 'btn-kamadenu-primary',
    'hp_s1_btn2_text'   => 'Sponsor a Cow',
    'hp_s1_btn2_link'   => '/Kamadenu/adopt.php',
    'hp_s1_btn2_class'  => 'btn-kamadenu-outline',
    'hp_s1_indicator'   => 'Sanctuary',
    // Hero Slide 2
    'hp_s2_bg'          => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=1600&q=80',
    'hp_s2_overlay'     => 'rgba(10,20,15,0.82)',
    'hp_s2_badge'       => 'SPONSOR & ADOPT A COW',
    'hp_s2_badge_color' => 'success',
    'hp_s2_badge_icon'  => 'fas fa-hand-holding-heart',
    'hp_s2_title'       => 'Adopt a Sacred Mother Cow Today',
    'hp_s2_subtitle'    => 'Experience the joy of cow adoption. Sponsor the monthly feeds and medical care costs for a resident cow and get regular updates from the sanctuary.',
    'hp_s2_phrase'      => '"ಲೋಕಾಃ ಸಮಸ್ತಾಃ ಸುಖಿನೋ ಭವಂತು"',
    'hp_s2_btn1_text'   => 'Sponsor a Cow',
    'hp_s2_btn1_link'   => '/Kamadenu/adopt.php',
    'hp_s2_btn1_class'  => 'btn-kamadenu-primary',
    'hp_s2_btn2_text'   => 'View Cow Passports',
    'hp_s2_btn2_link'   => '/Kamadenu/cows.php',
    'hp_s2_btn2_class'  => 'btn-kamadenu-outline',
    'hp_s2_indicator'   => 'Adopt a Cow',
    // Hero Slide 3
    'hp_s3_bg'          => 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=1600&q=80',
    'hp_s3_overlay'     => 'rgba(40,10,10,0.82)',
    'hp_s3_badge'       => 'EMERGENCY MEDICAL RELIEF',
    'hp_s3_badge_color' => 'danger',
    'hp_s3_badge_icon'  => 'fas fa-ambulance',
    'hp_s3_title'       => 'Rescue & Rehabilitate Street Cattle',
    'hp_s3_subtitle'    => 'Support our active emergency rescue campaigns. Your contributions provide shelter, critical surgery, and daily medication for injured and abandoned cows.',
    'hp_s3_phrase'      => '"ದಯವೇ ಧರ್ಮದ ಮೂಲವಯ್ಯಾ"',
    'hp_s3_btn1_text'   => 'Support Rescue Campaign',
    'hp_s3_btn1_link'   => '/Kamadenu/donate.php',
    'hp_s3_btn1_class'  => 'btn btn-danger rounded-pill px-4 py-3 font-ui fw-bold shadow',
    'hp_s3_btn2_text'   => '',
    'hp_s3_btn2_link'   => '',
    'hp_s3_btn2_class'  => '',
    'hp_s3_indicator'   => 'Rescue Relief',
    // Hero Slide 4
    'hp_s4_bg'          => 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=1600&q=80',
    'hp_s4_overlay'     => 'rgba(25,20,10,0.82)',
    'hp_s4_badge'       => 'SACRED DAILY GOUSEVA',
    'hp_s4_badge_color' => 'warning',
    'hp_s4_badge_icon'  => 'fas fa-pray',
    'hp_s4_title'       => 'Perform Sacred Gouseva Offerings',
    'hp_s4_subtitle'    => 'Sponsor nutritional green feeds, morning prayers (Grasa Seva), and Vedic Gou Pooja rituals at the sanctuary. Bring blessings, peace, and spiritual prosperity to your home.',
    'hp_s4_phrase'      => '"ಗೌ ಪೂಜಾ ಮಹತ್ತ್ವಂ"',
    'hp_s4_btn1_text'   => 'Sponsor a Seva',
    'hp_s4_btn1_link'   => '/Kamadenu/seva.php',
    'hp_s4_btn1_class'  => 'btn btn-warning rounded-pill px-4 py-3 font-ui fw-bold text-dark shadow',
    'hp_s4_btn2_text'   => '',
    'hp_s4_btn2_link'   => '',
    'hp_s4_btn2_class'  => '',
    'hp_s4_indicator'   => 'Gouseva Rituals',
    // Volunteer CTA Banner
    'hp_cta_title'      => 'Join Our Sacred Gouseva Movement',
    'hp_cta_subtitle'   => 'Become a Gousevak — volunteer your time, skills, or resources to protect and nurture our indigenous cow heritage.',
    'hp_cta_btn_text'   => 'Apply as Volunteer',
    'hp_cta_btn_link'   => '/Kamadenu/volunteer.php',
    // Stats Heading
    'hp_stats_title'    => 'Our Living Impact — Powered by Your Gouseva',
    'hp_stats_sub'      => 'REAL-TIME SANCTUARY METRICS',
    // About / Mission strip (optional)
    'hp_mission_text'   => 'Kamadenu Goushala is a sacred Vedic sanctuary dedicated to the protection, medical care, and dignified upkeep of indigenous Indian cattle. Founded on the principles of Gou Seva, we rescue abandoned, injured, and malnourished cows from streets and give them a peaceful forever home.',
];
ensure_hp_settings($pdo, $hp_defaults);

// ─── Save Handler ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_logo'])) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = '' WHERE setting_key = 'website_logo'");
        $stmt->execute();
        log_audit($pdo, 'Delete Custom Logo', 'settings');
        header("Location: /Kamadenu/admin/homepage-settings.php?saved=1");
        exit;
    }

    if (isset($_POST['save_hp'])) {
        $saved = 0;
        foreach ($_POST['hp'] as $key => $val) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ? AND setting_group = 'homepage'");
            $stmt->execute([trim($val), $key]);
            if ($stmt->rowCount()) $saved++;
        }

        // Handle background image uploads
        for ($s = 1; $s <= 4; $s++) {
            $field = "hp_slide{$s}_bg_file";
            $uploaded = handle_file_upload($field);
            if ($uploaded) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$uploaded, "hp_s{$s}_bg"]);
            }
        }

        // Handle website logo upload
        $logo_uploaded = handle_file_upload('logo_file');
        if ($logo_uploaded) {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'website_logo'");
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() == 0) {
                $stmt_ins = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES ('website_logo', ?, 'general')");
                $stmt_ins->execute([$logo_uploaded]);
            } else {
                $stmt_upd = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'website_logo'");
                $stmt_upd->execute([$logo_uploaded]);
            }
            $saved++;
        }

        log_audit($pdo, 'Update Homepage Settings', 'settings');
        header("Location: /Kamadenu/admin/homepage-settings.php?saved=1");
        exit;
    }
}

// ─── Load current settings ───────────────────────────────────────────────────
$hp = [];
$rows = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'homepage'")->fetchAll();
foreach ($rows as $r) {
    $hp[$r['setting_key']] = $r['setting_value'];
}
foreach ($hp_defaults as $k => $v) {
    if (!isset($hp[$k])) $hp[$k] = $v;
}

function hv($hp, $key, $default = '') { return htmlspecialchars($hp[$key] ?? $default, ENT_QUOTES); }

require_once __DIR__ . '/header.php';
?>

<style>
.hp-tab-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(245,158,11,0.2);
    border-radius: 14px;
    color: #94A3B8;
    padding: 12px 18px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}
.hp-tab-btn:hover { background: rgba(245,158,11,0.12); color: #F59E0B; border-color: rgba(245,158,11,0.4); }
.hp-tab-btn.active { background: linear-gradient(135deg, #F59E0B, #D97706); color: #0F172A; border-color: transparent; box-shadow: 0 4px 18px rgba(245,158,11,0.35); }
.hp-tab-pane { display: none; }
.hp-tab-pane.active { display: block; animation: fadeSlideIn 0.3s ease; }
@keyframes fadeSlideIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.slide-preview-wrap { border-radius: 18px; overflow: hidden; position: relative; min-height: 160px; background: #0B0F17; border: 1px solid rgba(245,158,11,0.15); }
.slide-preview-inner { padding: 24px; position: relative; z-index: 2; }
.slide-preview-bg { position: absolute; inset: 0; background-size: cover; background-position: center; border-radius: 18px; opacity: 0.45; }
.field-group { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px; margin-bottom: 20px; }
.field-group-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #F59E0B; margin-bottom: 14px; }
.badge-color-swatch { width: 20px; height: 20px; border-radius: 50%; display: inline-block; border: 2px solid rgba(255,255,255,0.2); }
.lang-tab-row { display: flex; gap: 6px; margin-bottom: 14px; }
.lang-pill { padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.04); color: #94A3B8; transition: all 0.2s; }
.lang-pill.active { background: rgba(245,158,11,0.2); border-color: #F59E0B; color: #F59E0B; }
.lang-block { display: none; }
.lang-block.active { display: block; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-heading mb-1"><i class="fas fa-home text-warning me-2"></i> Homepage Content Manager</h3>
        <p class="text-muted small mb-0">Full control over every visible element on the public homepage — backgrounds, texts, buttons, overlays, and more.</p>
    </div>
    <a href="/Kamadenu/index.php" target="_blank" class="btn btn-outline-info rounded-pill font-ui fw-bold px-4">
        <i class="fas fa-external-link-alt me-1"></i> Preview Site
    </a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success border-success shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i> Homepage settings saved successfully to MySQL database.
    </div>
<?php endif; ?>

<!-- Tab Nav -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <button class="hp-tab-btn active" onclick="switchTab(event,'slide1')"><i class="fas fa-layer-group"></i> Slide 1 — Welcome</button>
    <button class="hp-tab-btn" onclick="switchTab(event,'slide2')"><i class="fas fa-hand-holding-heart"></i> Slide 2 — Adopt</button>
    <button class="hp-tab-btn" onclick="switchTab(event,'slide3')"><i class="fas fa-ambulance"></i> Slide 3 — Rescue</button>
    <button class="hp-tab-btn" onclick="switchTab(event,'slide4')"><i class="fas fa-pray"></i> Slide 4 — Seva</button>
    <button class="hp-tab-btn" onclick="switchTab(event,'misc')"><i class="fas fa-palette"></i> CTA &amp; Stats</button>
    <button class="hp-tab-btn" onclick="switchTab(event,'logo')"><i class="fas fa-image"></i> Website Logo</button>
</div>

<div class="alert alert-info border-info d-flex align-items-center gap-3 mb-4 rounded-4 shadow-sm text-dark bg-info bg-opacity-10">
    <i class="fab fa-youtube fs-1 text-danger"></i>
    <div>
        <strong class="d-block text-dark fs-6">Homepage Video Gallery Link Template</strong>
        <span class="small text-muted">The YouTube videos shown in the "Sacred Video Gallery" section of the homepage are managed in a dedicated control panel. You can add new videos, delete old ones, or edit titles and links anytime. <a href="/Kamadenu/admin/videos.php" class="fw-bold text-info text-decoration-none">Manage Homepage Videos &amp; Links &rarr;</a></span>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="save_hp" value="1">

<?php
// Helper: render a standard text input row
function hp_field($label, $key, $hp, $placeholder = '') {
    echo '<div class="mb-3">';
    echo '<label class="form-label font-ui small fw-bold text-white-50">' . htmlspecialchars($label) . '</label>';
    echo '<input type="text" name="hp[' . htmlspecialchars($key) . ']" class="form-control font-ui" value="' . hv($hp, $key) . '" placeholder="' . htmlspecialchars($placeholder) . '">';
    echo '</div>';
}

function hp_textarea($label, $key, $hp, $rows = 3, $placeholder = '') {
    echo '<div class="mb-3">';
    echo '<label class="form-label font-ui small fw-bold text-white-50">' . htmlspecialchars($label) . '</label>';
    echo '<textarea name="hp[' . htmlspecialchars($key) . ']" class="form-control font-ui" rows="' . $rows . '" placeholder="' . htmlspecialchars($placeholder) . '">' . hv($hp, $key) . '</textarea>';
    echo '</div>';
}

function slide_section($num, $hp) {
    $s = "s{$num}";
    $slideName = ['', 'Welcome / Hero', 'Adopt a Cow', 'Rescue Campaign', 'Gouseva Seva'][$num];
    ?>
    <div class="hp-tab-pane <?php echo $num === 1 ? 'active' : ''; ?>" id="tab-slide<?php echo $num; ?>">
        <div class="row g-4">
            <!-- Left: Live Preview -->
            <div class="col-lg-4">
                <div class="slide-preview-wrap mb-4" id="preview<?php echo $num; ?>">
                    <div class="slide-preview-bg" id="previewBg<?php echo $num; ?>" style="background-image: url('<?php echo hv($hp, "hp_{$s}_bg"); ?>')"></div>
                    <div class="slide-preview-inner">
                        <span class="badge bg-<?php echo hv($hp, "hp_{$s}_badge_color"); ?> text-dark px-3 py-2 rounded-pill mb-3 d-inline-block font-ui fw-bold small">
                            <i class="<?php echo hv($hp, "hp_{$s}_badge_icon"); ?> me-1"></i>
                            <?php echo hv($hp, "hp_{$s}_badge"); ?>
                        </span>
                        <h4 class="font-heading text-white mb-2" style="font-size:1rem;"><?php echo hv($hp, "hp_{$s}_title"); ?></h4>
                        <p class="small text-white-50 mb-3"><?php echo mb_strimwidth(hv($hp, "hp_{$s}_subtitle"), 0, 80, '…'); ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-warning text-dark font-ui"><?php echo hv($hp, "hp_{$s}_btn1_text"); ?></span>
                            <?php if (hv($hp, "hp_{$s}_btn2_text")): ?>
                                <span class="badge bg-secondary text-white font-ui"><?php echo hv($hp, "hp_{$s}_btn2_text"); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-image me-1"></i> Background Image</div>
                    <?php hp_field('Image URL or Path', "hp_{$s}_bg", $hp, 'https://... or uploads/photo_...'); ?>
                    <label class="form-label font-ui small fw-bold text-white-50"><i class="fas fa-upload me-1 text-warning"></i> Or Upload Image</label>
                    <input type="file" name="hp_slide<?php echo $num; ?>_bg_file" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-2">Recommended: 1600×900px or wider. JPG, WebP or PNG.</small>
                </div>
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-circle-half-stroke me-1"></i> Overlay Color &amp; Opacity</div>
                    <?php hp_field('Overlay CSS (rgba)', "hp_{$s}_overlay", $hp, 'rgba(20,10,5,0.82)'); ?>
                    <small class="text-muted">Controls the dark color gradient on top of background. Use rgba(R,G,B,Opacity).</small>
                </div>
            </div>

            <!-- Right: Content Editor -->
            <div class="col-lg-8">
                <!-- Badge Chip -->
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-tag me-1"></i> Slide Badge / Chip</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?php hp_field('Badge Text', "hp_{$s}_badge", $hp, 'e.g. SACRED GOUSEVA SANCTUARY'); ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-ui small fw-bold text-white-50">Badge Color</label>
                            <select name="hp[hp_<?php echo $s; ?>_badge_color]" class="form-select">
                                <?php foreach (['warning'=>'Gold/Warning','success'=>'Green/Success','danger'=>'Red/Danger','info'=>'Blue/Info','primary'=>'Primary'] as $cv => $cl): ?>
                                    <option value="<?php echo $cv; ?>" <?php echo hv($hp, "hp_{$s}_badge_color") === $cv ? 'selected' : ''; ?>><?php echo $cl; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <?php hp_field('Icon Class', "hp_{$s}_badge_icon", $hp, 'fas fa-om'); ?>
                        </div>
                    </div>
                </div>

                <!-- Text Content (multi-lang) -->
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-language me-1"></i> Text Content — Multi-Language</div>
                    <div class="lang-tab-row">
                        <div class="lang-pill active" onclick="switchLang(this, 'en', <?php echo $num; ?>)">🇬🇧 English</div>
                        <div class="lang-pill" onclick="switchLang(this, 'kn', <?php echo $num; ?>)">🇮🇳 ಕನ್ನಡ</div>
                        <div class="lang-pill" onclick="switchLang(this, 'hi', <?php echo $num; ?>)">🇮🇳 हिंदी</div>
                    </div>
                    <?php foreach (['en' => 'English', 'kn' => 'ಕನ್ನಡ (Kannada)', 'hi' => 'हिंदी (Hindi)'] as $lang => $langLabel): ?>
                        <?php $suffix = ($lang === 'en') ? '' : '_' . $lang; ?>
                        <div class="lang-block <?php echo $lang === 'en' ? 'active' : ''; ?>" data-lang="<?php echo $lang; ?>" data-slide="<?php echo $num; ?>">
                            <div class="row g-3">
                                <div class="col-12">
                                    <?php hp_field("Slide Title ({$langLabel})", "hp_{$s}_title{$suffix}", $hp, 'Main hero heading for this slide'); ?>
                                </div>
                                <div class="col-12">
                                    <?php hp_textarea("Subtitle / Description ({$langLabel})", "hp_{$s}_subtitle{$suffix}", $hp, 3, 'Supporting text shown below the title'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php hp_field("Devotional Phrase ({$langLabel})", "hp_{$s}_phrase{$suffix}", $hp, '"ಗೋ ಮಾತಾ ಕಿ ಜೈ"'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Buttons -->
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-mouse-pointer me-1"></i> Action Buttons</div>
                    <div class="row g-3">
                        <div class="col-12"><strong class="small text-warning-emphasis">Primary Button (Left)</strong></div>
                        <div class="col-md-4">
                            <?php hp_field('Button 1 Text', "hp_{$s}_btn1_text", $hp, 'e.g. Donate Now'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php hp_field('Button 1 Link', "hp_{$s}_btn1_link", $hp, '/Kamadenu/donate.php'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php hp_field('Button 1 CSS Classes', "hp_{$s}_btn1_class", $hp, 'btn-kamadenu-primary'); ?>
                        </div>
                        <div class="col-12"><strong class="small text-muted">Secondary Button (Right) — leave blank to hide</strong></div>
                        <div class="col-md-4">
                            <?php hp_field('Button 2 Text', "hp_{$s}_btn2_text", $hp, 'e.g. View Cows'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php hp_field('Button 2 Link', "hp_{$s}_btn2_link", $hp, '/Kamadenu/cows.php'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php hp_field('Button 2 CSS Classes', "hp_{$s}_btn2_class", $hp, 'btn-kamadenu-outline'); ?>
                        </div>
                    </div>
                </div>

                <!-- Indicator Label -->
                <div class="field-group">
                    <div class="field-group-title"><i class="fas fa-ellipsis-h me-1"></i> Slide Indicator Tab Label</div>
                    <div class="col-md-5">
                        <?php hp_field('Indicator Text', "hp_{$s}_indicator", $hp, 'e.g. Sanctuary'); ?>
                    </div>
                    <small class="text-muted">Short label displayed in the bottom slide navigation tabs on the homepage.</small>
                </div>
            </div>
        </div>
    </div>
    <?php
}

for ($i = 1; $i <= 4; $i++) slide_section($i, $hp);
?>

<!-- CTA & Stats Tab -->
<div class="hp-tab-pane" id="tab-misc">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-hands-helping me-1"></i> Volunteer CTA Banner</div>
                <?php hp_field('CTA Title', 'hp_cta_title', $hp, 'Join Our Sacred Gouseva Movement'); ?>
                <?php hp_textarea('CTA Subtitle / Description', 'hp_cta_subtitle', $hp, 3); ?>
                <?php hp_field('CTA Button Text', 'hp_cta_btn_text', $hp, 'Apply as Volunteer'); ?>
                <?php hp_field('CTA Button Link', 'hp_cta_btn_link', $hp, '/Kamadenu/volunteer.php'); ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-chart-bar me-1"></i> Stats Section Heading</div>
                <?php hp_field('Stats Sub-label (small text)', 'hp_stats_sub', $hp, 'REAL-TIME SANCTUARY METRICS'); ?>
                <?php hp_field('Stats Main Title', 'hp_stats_title', $hp, 'Our Living Impact — Powered by Your Gouseva'); ?>
            </div>
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-bullhorn me-1"></i> Mission Text Strip</div>
                <?php hp_textarea('Mission Statement Paragraph', 'hp_mission_text', $hp, 4, 'A short paragraph describing the Goushala mission...'); ?>
                <small class="text-muted">Displayed in the "About" or intro paragraph section on the homepage if used.</small>
            </div>
        </div>
    </div>
</div>

<!-- Website Logo Tab -->
<div class="hp-tab-pane" id="tab-logo">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-image me-1"></i> Upload Logo (Add / Edit)</div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold text-white-50">Choose Image File</label>
                    <input type="file" name="logo_file" class="form-control font-ui" accept="image/*">
                    <div class="form-text text-muted small mt-1">Supported formats: PNG, JPG, JPEG, GIF, WEBP, SVG. Recommended height: 48px to 80px (horizontal aspect ratio).</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-eye me-1"></i> Current Logo Status</div>
                <div class="mb-3 p-4 bg-black bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <?php
                    $logo_setting = get_setting($pdo, 'website_logo', '');
                    if (!empty($logo_setting)):
                        $logo_url = img_url($logo_setting);
                    ?>
                        <div class="mb-3 text-muted small">Active Custom Logo:</div>
                        <div class="p-3 bg-white d-inline-block rounded shadow-sm mb-3">
                            <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Active Custom Logo" style="max-height: 80px; width: auto; object-fit: contain;">
                        </div>
                        <div class="text-danger small mb-3"><?php echo htmlspecialchars($logo_setting); ?></div>
                        <button type="submit" name="delete_logo" value="1" class="btn btn-outline-danger btn-sm font-ui fw-bold px-3">
                            <i class="fas fa-trash-alt me-1.5"></i> Delete Logo (Reset to Default)
                        </button>
                    <?php else: ?>
                        <div class="mb-3 text-muted small">Using Default Assets Logo:</div>
                        <div class="p-3 bg-white d-inline-block rounded shadow-sm mb-3">
                            <img src="/Kamadenu/css/cowlogo.jpg" alt="Default Logo" style="max-height: 80px; width: auto; object-fit: contain;">
                        </div>
                        <div class="text-muted small">/Kamadenu/css/cowlogo.jpg</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Button -->
<div class="mt-4 pb-4">
    <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-5 py-3 shadow fs-5">
        <i class="fas fa-save me-2"></i> Save All Homepage Settings
    </button>
    <a href="/Kamadenu/index.php" target="_blank" class="btn btn-outline-info ms-2 px-4 py-3 font-ui fw-bold">
        <i class="fas fa-eye me-2"></i> Live Preview
    </a>
</div>

</form>

<script>
function switchTab(e, id) {
    document.querySelectorAll('.hp-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.hp-tab-pane').forEach(p => p.classList.remove('active'));
    e.currentTarget.classList.add('active');
    document.getElementById('tab-' + id).classList.add('active');
}

function switchLang(el, lang, slideNum) {
    const parentGroup = el.closest('.field-group');
    parentGroup.querySelectorAll('.lang-pill').forEach(p => p.classList.remove('active'));
    parentGroup.querySelectorAll('.lang-block').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    parentGroup.querySelector(`.lang-block[data-lang="${lang}"][data-slide="${slideNum}"]`).classList.add('active');
}

// Live preview background update
document.querySelectorAll('input[name$="_bg]"]').forEach(input => {
    input.addEventListener('input', function() {
        const match = this.name.match(/hp_s(\d)_bg/);
        if (match) {
            const previewBg = document.getElementById('previewBg' + match[1]);
            if (previewBg) previewBg.style.backgroundImage = `url('${this.value}')`;
        }
    });
});

// Image upload live preview
document.querySelectorAll('input[type="file"][name^="hp_slide"]').forEach(inp => {
    inp.addEventListener('change', function() {
        const match = this.name.match(/hp_slide(\d)/);
        if (match && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const previewBg = document.getElementById('previewBg' + match[1]);
                if (previewBg) previewBg.style.backgroundImage = `url('${e.target.result}')`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
