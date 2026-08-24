<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

// ─── Default About Page Settings (Self-Healing Bootstrap) ───────────────────
$about_defaults = [
    // English
    'about_title_en'           => 'About Kamadenu Goushala Trust',
    'about_subtitle_en'        => 'Our sacred mission to protect indigenous Indian cows, restore traditional Gouseva, and foster organic bio-farming.',
    'about_heritage_title_en'  => 'Goushala Heritage & Spiritual Legacy',
    'about_heritage_text1_en'  => 'Kamadenu Goushala Trust was founded under the shelter of ancient banyan trees with a single divine objective: to provide lifetime sanctuary, compassionate medical care, and reverent daily Gouseva to stray, elderly, and rescued indigenous Indian cows.',
    'about_heritage_text2_en'  => 'In ancient Vedic tradition, the cow is revered as Kamadenu — the mother of all beings who embodies prosperity, purity, and spiritual grace.',
    'about_vows_title_en'      => 'Our 5 Sacred Vows (Panch Vratas)',

    // Kannada
    'about_title_kn'           => 'ಕಾಮಧೇನು ಗೋಶಾಲೆ ಟ್ರಸ್ಟ್ ಬಗ್ಗೆ',
    'about_subtitle_kn'        => 'ದೇಶಿ ಗೋವುಗಳನ್ನು ರಕ್ಷಿಸುವುದು, ಸಾಂಪ್ರದಾಯಿಕ ಗೋಸೇವೆಯನ್ನು ಪುನರುಜ್ಜೀವನಗೊಳಿಸುವುದು ಮತ್ತು ಸಾವಯವ ಕೃಷಿಯನ್ನು ಪ್ರೋತ್ಸಾಹಿಸುವುದು ನಮ್ಮ ಪವಿತ್ರ ಧ್ಯೇಯ.',
    'about_heritage_title_kn'  => 'ಗೋಶಾಲೆಯ ಪರಂಪರೆ ಮತ್ತು ಧಾರ್ಮಿಕ ಹಿನ್ನೆಲೆ',
    'about_heritage_text1_kn'  => 'ಕಾಮಧೇನು ಗೋಶಾಲೆ ಟ್ರಸ್ಟ್ ಅನಾಥ, ವೃದ್ಧ ಮತ್ತು ಗಾಯಗೊಂಡ ದೇಶೀಯ ಗೋವುಗಳಿಗೆ ಜೀವಮಾನದ ಆಶ್ರಯ, ಉಚಿತ ವೈದ್ಯಕೀಯ ಸೇವೆ ಮತ್ತು ದೈನಂದಿನ ಪವಿತ್ರ ಗೋಸೇವೆಯನ್ನು ಒದಗಿಸುವ ಏಕೈಕ ದಿವ್ಯ ಉದ್ದೇಶದಿಂದ ಸ್ಥಾಪಿತವಾಗಿದೆ.',
    'about_heritage_text2_kn'  => 'ವೈದಿಕ ಪರಂಪರೆಯಲ್ಲಿ ಗೋವನ್ನು ಕಾಮಧೇನು ಎಂದು ಪೂಜಿಸಲಾಗುತ್ತದೆ - ಸಕಲ ಜೀವಜಾಲದ ತಾಯಿ ಮತ್ತು ಸಮೃದ್ಧಿಯ ಸಂಕೇತ.',
    'about_vows_title_kn'      => 'ನಮ್ಮ ೫ ಪವಿತ್ರ ವ್ರತಗಳು (ಪಂಚ ವ್ರತಗಳು)',

    // Hindi
    'about_title_hi'           => 'कामधेनु गौशाला ट्रस्ट के बारे में',
    'about_subtitle_hi'        => 'देशी गायों की रक्षा करना, पारंपरिक गौसेवा को पुनर्जीवित करना और जैविक कृषि को बढ़ावा देना हमारा पवित्र मिशन है।',
    'about_heritage_title_hi'  => 'गौशाला की विरासत एवं आध्यात्मिक पृष्ठभूमि',
    'about_heritage_text1_hi'  => 'कामधेनु गौशाला ट्रस्ट की स्थापना असहाय, वृद्ध एवं बीमार देशी गायों को आजीवन आश्रय, चिकित्सा उपचार एवं सेवा प्रदान करने के उद्देश्य से की गई थी।',
    'about_heritage_text2_hi'  => 'वैदिक परंपरा में गाय को कामधेनु के रूप में पूजा जाता है - सभी जीवों की माता एवं समृद्धि का प्रतीक।',
    'about_vows_title_hi'      => 'हमारे ५ पवित्र संकल्प (पंच व्रत)',

    // Heritage Image Default
    'about_heritage_image'     => 'assets/images/goushala-heritage.jpg'
];

// Ensure all settings keys exist in settings table
foreach ($about_defaults as $key => $val) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        if ($stmt->fetchColumn() == 0) {
            $stmt_i = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, 'about')");
            $stmt_i->execute([$key, $val]);
        }
    } catch (Exception $e) {}
}

// ─── Save Handler ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_heritage_image'])) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = '' WHERE setting_key = 'about_heritage_image'");
        $stmt->execute();
        log_audit($pdo, 'Delete Custom About Heritage Image', 'settings');
        header("Location: /Kamadenu/admin/about-settings.php?saved=1");
        exit;
    }

    if (isset($_POST['save_about'])) {
        $saved = 0;
        foreach ($_POST['about'] as $key => $val) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ? AND setting_group = 'about'");
            $stmt->execute([trim($val), $key]);
            if ($stmt->rowCount()) $saved++;
        }

        // Handle heritage image file upload
        $image_uploaded = handle_file_upload('heritage_image_file');
        if ($image_uploaded) {
            $stmt_upd = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'about_heritage_image'");
            $stmt_upd->execute([$image_uploaded]);
            $saved++;
        }

        log_audit($pdo, 'Update About Page Settings', 'settings');
        header("Location: /Kamadenu/admin/about-settings.php?saved=1");
        exit;
    }
}

// ─── Load current settings ───────────────────────────────────────────────────
$about_settings = [];
$rows = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'about'")->fetchAll();
foreach ($rows as $r) {
    $about_settings[$r['setting_key']] = $r['setting_value'];
}
foreach ($about_defaults as $k => $v) {
    if (!isset($about_settings[$k])) $about_settings[$k] = $v;
}

function ab_val($settings, $key, $default = '') {
    return htmlspecialchars($settings[$key] ?? $default, ENT_QUOTES);
}

require_once __DIR__ . '/header.php';
?>

<style>
.about-tab-btn {
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
.about-tab-btn:hover { background: rgba(245,158,11,0.12); color: #F59E0B; border-color: rgba(245,158,11,0.4); }
.about-tab-btn.active { background: linear-gradient(135deg, #F59E0B, #D97706); color: #0F172A; border-color: transparent; box-shadow: 0 4px 18px rgba(245,158,11,0.35); }
.about-tab-pane { display: none; }
.about-tab-pane.active { display: block; animation: fadeSlideIn 0.3s ease; }
@keyframes fadeSlideIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.field-group { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px; margin-bottom: 20px; }
.field-group-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #F59E0B; margin-bottom: 14px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-heading mb-1"><i class="fas fa-info-circle text-warning me-2"></i> About Us Page Editor</h3>
        <p class="text-muted small mb-0">Manage the history, heritage details, mission vows, and illustrations of the trust's About Us section.</p>
    </div>
    <a href="/Kamadenu/about.php" target="_blank" class="btn btn-outline-info rounded-pill font-ui fw-bold px-4">
        <i class="fas fa-external-link-alt me-1"></i> Preview About Page
    </a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success border-success shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i> About page settings successfully saved.
    </div>
<?php endif; ?>

<!-- Tab Navigation -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <button class="about-tab-btn active" onclick="switchAboutTab(event,'en')"><i class="fas fa-globe-americas"></i> English (EN)</button>
    <button class="about-tab-btn" onclick="switchAboutTab(event,'kn')"><i class="fas fa-language"></i> Kannada (KN)</button>
    <button class="about-tab-btn" onclick="switchAboutTab(event,'hi')"><i class="fas fa-globe-asia"></i> Hindi (HI)</button>
    <button class="about-tab-btn" onclick="switchAboutTab(event,'media')"><i class="fas fa-photo-video"></i> Page Media &amp; Images</button>
</div>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="save_about" value="1">

<?php
// Helper to render editable texts
function about_field($label, $key, $settings, $placeholder = '') {
    echo '<div class="mb-3">';
    echo '<label class="form-label font-ui small fw-bold text-white-50">' . htmlspecialchars($label) . '</label>';
    echo '<input type="text" name="about[' . htmlspecialchars($key) . ']" class="form-control font-ui" value="' . ab_val($settings, $key) . '" placeholder="' . htmlspecialchars($placeholder) . '">';
    echo '</div>';
}

function about_textarea($label, $key, $settings, $rows = 3, $placeholder = '') {
    echo '<div class="mb-3">';
    echo '<label class="form-label font-ui small fw-bold text-white-50">' . htmlspecialchars($label) . '</label>';
    echo '<textarea name="about[' . htmlspecialchars($key) . ']" class="form-control font-ui" rows="' . $rows . '" placeholder="' . htmlspecialchars($placeholder) . '">' . ab_val($settings, $key) . '</textarea>';
    echo '</div>';
}
?>

<!-- English Tab -->
<div class="about-tab-pane active" id="tab-en">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-heading me-1"></i> Header Block Configuration (EN)</div>
                <?php about_field('Page Title (English)', 'about_title_en', $about_settings, 'About Kamadenu Goushala Trust'); ?>
                <?php about_textarea('Subtitle / Description (English)', 'about_subtitle_en', $about_settings, 3); ?>
            </div>
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-bookmark me-1"></i> Core Principles Title (EN)</div>
                <?php about_field('Principles / Vows Title (English)', 'about_vows_title_en', $about_settings, 'Our 5 Sacred Vows (Panch Vratas)'); ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-history me-1"></i> Goushala Heritage Section (EN)</div>
                <?php about_field('Heritage Title (English)', 'about_heritage_title_en', $about_settings, 'Goushala Heritage & Spiritual Legacy'); ?>
                <?php about_textarea('Heritage Text Paragraph 1 (English)', 'about_heritage_text1_en', $about_settings, 4); ?>
                <?php about_textarea('Heritage Text Paragraph 2 (English)', 'about_heritage_text2_en', $about_settings, 4); ?>
            </div>
        </div>
    </div>
</div>

<!-- Kannada Tab -->
<div class="about-tab-pane" id="tab-kn">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-heading me-1"></i> Header Block Configuration (KN)</div>
                <?php about_field('Page Title (Kannada)', 'about_title_kn', $about_settings, 'ಕಾಮಧೇನು ಗೋಶಾಲೆ ಟ್ರಸ್ಟ್ ಬಗ್ಗೆ'); ?>
                <?php about_textarea('Subtitle / Description (Kannada)', 'about_subtitle_kn', $about_settings, 3); ?>
            </div>
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-bookmark me-1"></i> Core Principles Title (KN)</div>
                <?php about_field('Principles / Vows Title (Kannada)', 'about_vows_title_kn', $about_settings, 'ನಮ್ಮ ೫ ಪವಿತ್ರ ವ್ರತಗಳು (ಪಂಚ ವ್ರತಗಳು)'); ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-history me-1"></i> Goushala Heritage Section (KN)</div>
                <?php about_field('Heritage Title (Kannada)', 'about_heritage_title_kn', $about_settings, 'ಗೋಶಾಲೆಯ ಪರಂಪರೆ ಮತ್ತು ಧಾರ್ಮಿಕ ಹಿನ್ನೆಲೆ'); ?>
                <?php about_textarea('Heritage Text Paragraph 1 (Kannada)', 'about_heritage_text1_kn', $about_settings, 4); ?>
                <?php about_textarea('Heritage Text Paragraph 2 (Kannada)', 'about_heritage_text2_kn', $about_settings, 4); ?>
            </div>
        </div>
    </div>
</div>

<!-- Hindi Tab -->
<div class="about-tab-pane" id="tab-hi">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-heading me-1"></i> Header Block Configuration (HI)</div>
                <?php about_field('Page Title (Hindi)', 'about_title_hi', $about_settings, 'कामधेनु गौशाला ट्रस्ट के बारे में'); ?>
                <?php about_textarea('Subtitle / Description (Hindi)', 'about_subtitle_hi', $about_settings, 3); ?>
            </div>
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-bookmark me-1"></i> Core Principles Title (HI)</div>
                <?php about_field('Principles / Vows Title (Hindi)', 'about_vows_title_hi', $about_settings, 'हमारे ५ पवित्र संकल्प (पंच व्रत)'); ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-history me-1"></i> Goushala Heritage Section (HI)</div>
                <?php about_field('Heritage Title (Hindi)', 'about_heritage_title_hi', $about_settings, 'गौशाला की विरासत एवं आध्यात्मिक पृष्ठभूमि'); ?>
                <?php about_textarea('Heritage Text Paragraph 1 (Hindi)', 'about_heritage_text1_hi', $about_settings, 4); ?>
                <?php about_textarea('Heritage Text Paragraph 2 (Hindi)', 'about_heritage_text2_hi', $about_settings, 4); ?>
            </div>
        </div>
    </div>
</div>

<!-- Page Media Tab -->
<div class="about-tab-pane" id="tab-media">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-image me-1"></i> Heritage Image Uploader</div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold text-white-50">Upload New Heritage Photo</label>
                    <input type="file" name="heritage_image_file" class="form-control font-ui" accept="image/*">
                    <div class="form-text text-muted small mt-1">Replaces the side banner photo in the Heritage Section. Recommended resolution: ~800x600px.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="field-group">
                <div class="field-group-title"><i class="fas fa-eye me-1"></i> Current Heritage Image Status</div>
                <div class="mb-3 p-4 bg-black bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <?php
                    $heritage_image = get_setting($pdo, 'about_heritage_image', '');
                    if (!empty($heritage_image)):
                        $img_url = img_url($heritage_image);
                    ?>
                        <div class="mb-3 text-muted small">Active Custom Image:</div>
                        <img src="<?php echo htmlspecialchars($img_url); ?>" alt="Heritage Preview" class="img-fluid rounded border p-1 bg-white mb-3" style="max-height: 200px; object-fit: cover;">
                        <div class="text-danger small mb-3"><?php echo htmlspecialchars($heritage_image); ?></div>
                        <button type="submit" name="delete_heritage_image" value="1" class="btn btn-outline-danger btn-sm font-ui fw-bold px-3">
                            <i class="fas fa-trash-alt me-1.5"></i> Reset to Default Heritage Photo
                        </button>
                    <?php else: ?>
                        <div class="mb-3 text-muted small">Using Default Heritage Photo:</div>
                        <img src="/Kamadenu/assets/images/goushala-heritage.jpg" alt="Default Heritage Preview" class="img-fluid rounded border p-1 bg-white mb-3" style="max-height: 200px; object-fit: cover;">
                        <div class="text-muted small">/Kamadenu/assets/images/goushala-heritage.jpg</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Button Row -->
<div class="mt-4 pb-4">
    <button type="submit" class="btn btn-kamadenu-primary font-ui fw-bold px-5 py-3 shadow fs-5">
        <i class="fas fa-save me-2"></i> Save All About Page Settings
    </button>
    <a href="/Kamadenu/about.php" target="_blank" class="btn btn-outline-info ms-2 px-4 py-3 font-ui fw-bold">
        <i class="fas fa-eye me-2"></i> Preview About Page
    </a>
</div>

</form>

<script>
function switchAboutTab(e, id) {
    document.querySelectorAll('.about-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.about-tab-pane').forEach(p => p.classList.remove('active'));
    e.currentTarget.classList.add('active');
    document.getElementById('tab-' + id).classList.add('active');
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
