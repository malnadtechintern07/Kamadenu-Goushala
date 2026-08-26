<?php
require_once __DIR__ . '/../config/database.php';
require_admin_login($pdo);

// Helper function to save setting if key exists or insert if missing
function save_button_setting($pdo, $key, $value, $group = 'buttons', $label = '') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    if ($stmt->fetchColumn() > 0) {
        $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")->execute([$value, $key]);
    } else {
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, setting_label) VALUES (?, ?, ?, ?)")->execute([$key, $value, $group, $label]);
    }
}

// Default Button Configuration Values
$defaults = [
    'btn_primary_label' => 'Sponsor Cow',
    'btn_primary_bg_start' => '#e67e22',
    'btn_primary_bg_end' => '#d35400',
    'btn_primary_text_color' => '#ffffff',
    'btn_primary_border_color' => '#ffd700',
    'btn_primary_border_radius' => '50px',

    'btn_feed_label' => 'Feed Cow',
    'btn_feed_bg_start' => '#10b981',
    'btn_feed_bg_end' => '#059669',
    'btn_feed_text_color' => '#ffffff',
    'btn_feed_border_color' => '#6ee7b7',
    'btn_feed_border_radius' => '50px',

    'btn_whatsapp_label' => 'Sponsor via WhatsApp',
    'btn_wa_bg_start' => '#16a34a',
    'btn_wa_bg_end' => '#15803d',
    'btn_wa_text_color' => '#ffffff',
    'btn_wa_border_color' => '#86efac',
    'btn_wa_border_radius' => '50px',

    'btn_details_label' => 'Cow Details',
    'btn_details_bg_start' => '#1e293b',
    'btn_details_bg_end' => '#0f172a',
    'btn_details_text_color' => '#f8fafc',
    'btn_details_border_color' => '#38bdf8',
    'btn_details_border_radius' => '50px',

    'btn_cart_label' => 'Add to Cart',
    'btn_cart_bg_start' => '#fef3c7',
    'btn_cart_bg_end' => '#fde68a',
    'btn_cart_text_color' => '#b45309',
    'btn_cart_border_color' => '#f59e0b',
    'btn_cart_border_radius' => '50px'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_button_settings'])) {
    foreach ($defaults as $key => $def_val) {
        $val = isset($_POST[$key]) ? trim($_POST[$key]) : $def_val;
        save_button_setting($pdo, $key, $val, 'buttons', ucwords(str_replace('_', ' ', $key)));
    }
    log_audit($pdo, 'Update Button Customizer Settings', 'settings');
    header("Location: /Kamadenu/admin/button-settings.php?saved=1");
    exit;
}

// Fetch current values
$button_cfg = [];
foreach ($defaults as $key => $def_val) {
    $button_cfg[$key] = get_setting($pdo, $key, $def_val);
}

require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-heading mb-1"><i class="fas fa-paint-brush text-warning me-2"></i> Button Customizer & Appearance Manager</h3>
        <p class="text-muted small mb-0">Customize website button labels, gradient colors, borders, and rounded shapes with real-time live preview.</p>
    </div>
</div>

<?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
    <div class="alert alert-success shadow-sm border-success mb-4 font-ui">
        <i class="fas fa-check-circle me-2"></i> Button styling and dynamic website appearance saved successfully to MySQL!
    </div>
<?php endif; ?>

<form method="POST" id="btnCustomizerForm">
    <input type="hidden" name="save_button_settings" value="1">

    <!-- Preset Quick Themes Toolbar -->
    <div class="kamadenu-card p-3 mb-4 bg-dark text-white border-warning">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="font-ui fw-bold text-warning"><i class="fas fa-magic me-1.5"></i> Preset Quick Themes:</span>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill font-ui fw-bold" onclick="applyPreset('terracotta')">Royal Terracotta & Gold</button>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill font-ui fw-bold" onclick="applyPreset('emerald')">Emerald Sanctuary</button>
                <button type="button" class="btn btn-sm btn-outline-info rounded-pill font-ui fw-bold" onclick="applyPreset('sapphire')">Midnight Sapphire</button>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill font-ui fw-bold" onclick="applyPreset('saffron')">Vedic Saffron</button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Button Customization Controls -->
        <div class="col-lg-7">
            
            <!-- 1. Sponsor / Primary Buttons -->
            <div class="kamadenu-card p-4 mb-4 border-warning">
                <h5 class="font-heading text-warning mb-3 pb-2 border-bottom">
                    <i class="fas fa-heart me-2"></i> 1. Sponsor Cow / Primary Button (.btn-kamadenu-primary)
                </h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold">Button Default Label Text</label>
                        <input type="text" name="btn_primary_label" id="btn_primary_label" class="form-control" value="<?php echo e($button_cfg['btn_primary_label']); ?>" oninput="updateLivePreview()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient Start Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_primary_bg_start" id="btn_primary_bg_start" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_primary_bg_start']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_primary_bg_start_hex" value="<?php echo e($button_cfg['btn_primary_bg_start']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient End Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_primary_bg_end" id="btn_primary_bg_end" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_primary_bg_end']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_primary_bg_end_hex" value="<?php echo e($button_cfg['btn_primary_bg_end']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Text Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_primary_text_color" id="btn_primary_text_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_primary_text_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_primary_text_color_hex" value="<?php echo e($button_cfg['btn_primary_text_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Border Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_primary_border_color" id="btn_primary_border_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_primary_border_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_primary_border_color_hex" value="<?php echo e($button_cfg['btn_primary_border_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold d-flex justify-content-between">
                            <span>Border Radius / Corner Shape</span>
                            <span id="btn_primary_radius_val" class="font-mono text-warning"><?php echo e($button_cfg['btn_primary_border_radius']); ?></span>
                        </label>
                        <input type="range" name="btn_primary_border_radius" id="btn_primary_border_radius" class="form-range" min="0" max="50" step="2" value="<?php echo intval($button_cfg['btn_primary_border_radius']); ?>" oninput="updateLivePreview()">
                    </div>
                </div>
            </div>

            <!-- 2. Feed Cow Buttons -->
            <div class="kamadenu-card p-4 mb-4 border-success">
                <h5 class="font-heading text-success mb-3 pb-2 border-bottom">
                    <i class="fas fa-cookie-bite me-2"></i> 2. Feed Cow Button (.btn-feed-cow)
                </h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold">Button Default Label Text</label>
                        <input type="text" name="btn_feed_label" id="btn_feed_label" class="form-control" value="<?php echo e($button_cfg['btn_feed_label']); ?>" oninput="updateLivePreview()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient Start Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_feed_bg_start" id="btn_feed_bg_start" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_feed_bg_start']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_feed_bg_start_hex" value="<?php echo e($button_cfg['btn_feed_bg_start']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient End Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_feed_bg_end" id="btn_feed_bg_end" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_feed_bg_end']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_feed_bg_end_hex" value="<?php echo e($button_cfg['btn_feed_bg_end']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Text Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_feed_text_color" id="btn_feed_text_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_feed_text_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_feed_text_color_hex" value="<?php echo e($button_cfg['btn_feed_text_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Border Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_feed_border_color" id="btn_feed_border_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_feed_border_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_feed_border_color_hex" value="<?php echo e($button_cfg['btn_feed_border_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold d-flex justify-content-between">
                            <span>Border Radius / Corner Shape</span>
                            <span id="btn_feed_radius_val" class="font-mono text-success"><?php echo e($button_cfg['btn_feed_border_radius']); ?></span>
                        </label>
                        <input type="range" name="btn_feed_border_radius" id="btn_feed_border_radius" class="form-range" min="0" max="50" step="2" value="<?php echo intval($button_cfg['btn_feed_border_radius']); ?>" oninput="updateLivePreview()">
                    </div>
                </div>
            </div>

            <!-- 3. Add to Cart / Store Buttons -->
            <div class="kamadenu-card p-4 mb-4 border-warning">
                <h5 class="font-heading text-warning mb-3 pb-2 border-bottom">
                    <i class="fas fa-shopping-cart me-2"></i> 3. Add to Cart Button (.btn-cart / .btn-kamadenu-outline)
                </h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold">Button Default Label Text</label>
                        <input type="text" name="btn_cart_label" id="btn_cart_label" class="form-control" value="<?php echo e($button_cfg['btn_cart_label']); ?>" oninput="updateLivePreview()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient Start Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_cart_bg_start" id="btn_cart_bg_start" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_cart_bg_start']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_cart_bg_start_hex" value="<?php echo e($button_cfg['btn_cart_bg_start']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient End Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_cart_bg_end" id="btn_cart_bg_end" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_cart_bg_end']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_cart_bg_end_hex" value="<?php echo e($button_cfg['btn_cart_bg_end']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Text Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_cart_text_color" id="btn_cart_text_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_cart_text_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_cart_text_color_hex" value="<?php echo e($button_cfg['btn_cart_text_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Border Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_cart_border_color" id="btn_cart_border_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_cart_border_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_cart_border_color_hex" value="<?php echo e($button_cfg['btn_cart_border_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold d-flex justify-content-between">
                            <span>Border Radius / Corner Shape</span>
                            <span id="btn_cart_radius_val" class="font-mono text-warning"><?php echo e($button_cfg['btn_cart_border_radius']); ?></span>
                        </label>
                        <input type="range" name="btn_cart_border_radius" id="btn_cart_border_radius" class="form-range" min="0" max="50" step="2" value="<?php echo intval($button_cfg['btn_cart_border_radius']); ?>" oninput="updateLivePreview()">
                    </div>
                </div>
            </div>

            <!-- 4. WhatsApp Buttons -->
            <div class="kamadenu-card p-4 mb-4 border-success">
                <h5 class="font-heading text-success mb-3 pb-2 border-bottom">
                    <i class="fab fa-whatsapp me-2"></i> 4. WhatsApp Payment Button (.btn-whatsapp / .btn-success)
                </h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold">Button Default Label Text</label>
                        <input type="text" name="btn_whatsapp_label" id="btn_whatsapp_label" class="form-control" value="<?php echo e($button_cfg['btn_whatsapp_label']); ?>" oninput="updateLivePreview()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient Start Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_wa_bg_start" id="btn_wa_bg_start" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_wa_bg_start']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_wa_bg_start_hex" value="<?php echo e($button_cfg['btn_wa_bg_start']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient End Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_wa_bg_end" id="btn_wa_bg_end" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_wa_bg_end']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_wa_bg_end_hex" value="<?php echo e($button_cfg['btn_wa_bg_end']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Text Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_wa_text_color" id="btn_wa_text_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_wa_text_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_wa_text_color_hex" value="<?php echo e($button_cfg['btn_wa_text_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Border Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_wa_border_color" id="btn_wa_border_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_wa_border_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_wa_border_color_hex" value="<?php echo e($button_cfg['btn_wa_border_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold d-flex justify-content-between">
                            <span>Border Radius / Corner Shape</span>
                            <span id="btn_wa_radius_val" class="font-mono text-success"><?php echo e($button_cfg['btn_wa_border_radius']); ?></span>
                        </label>
                        <input type="range" name="btn_wa_border_radius" id="btn_wa_border_radius" class="form-range" min="0" max="50" step="2" value="<?php echo intval($button_cfg['btn_wa_border_radius']); ?>" oninput="updateLivePreview()">
                    </div>
                </div>
            </div>

            <!-- 5. Cow Details Buttons -->
            <div class="kamadenu-card p-4 mb-4 border-info">
                <h5 class="font-heading text-info mb-3 pb-2 border-bottom">
                    <i class="fas fa-info-circle me-2"></i> 5. Cow Details Button (.btn-cow-details)
                </h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold">Button Default Label Text</label>
                        <input type="text" name="btn_details_label" id="btn_details_label" class="form-control" value="<?php echo e($button_cfg['btn_details_label']); ?>" oninput="updateLivePreview()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient Start Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_details_bg_start" id="btn_details_bg_start" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_details_bg_start']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_details_bg_start_hex" value="<?php echo e($button_cfg['btn_details_bg_start']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Gradient End Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_details_bg_end" id="btn_details_bg_end" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_details_bg_end']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_details_bg_end_hex" value="<?php echo e($button_cfg['btn_details_bg_end']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Text Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_details_text_color" id="btn_details_text_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_details_text_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_details_text_color_hex" value="<?php echo e($button_cfg['btn_details_text_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-ui small fw-bold">Border Color</label>
                        <div class="input-group">
                            <input type="color" name="btn_details_border_color" id="btn_details_border_color" class="form-control form-control-color" value="<?php echo e($button_cfg['btn_details_border_color']); ?>" oninput="updateLivePreview()">
                            <input type="text" class="form-control font-mono" id="btn_details_border_color_hex" value="<?php echo e($button_cfg['btn_details_border_color']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-ui small fw-bold d-flex justify-content-between">
                            <span>Border Radius / Corner Shape</span>
                            <span id="btn_details_radius_val" class="font-mono text-info"><?php echo e($button_cfg['btn_details_border_radius']); ?></span>
                        </label>
                        <input type="range" name="btn_details_border_radius" id="btn_details_border_radius" class="form-range" min="0" max="50" step="2" value="<?php echo intval($button_cfg['btn_details_border_radius']); ?>" oninput="updateLivePreview()">
                    </div>
                </div>
            </div>

            <!-- Save Submit Bar -->
            <button type="submit" class="btn btn-warning btn-lg w-100 py-3 font-ui fw-bold shadow">
                <i class="fas fa-save me-2"></i> Save All Button Styles &amp; Apply to Website
            </button>

        </div>

        <!-- Right Column: Sticky Live Real-Time Preview Card -->
        <div class="col-lg-5">
            <div class="kamadenu-card p-4 sticky-top" style="top: 100px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <h5 class="font-heading mb-0 text-dark"><i class="fas fa-eye text-warning me-2"></i> Real-Time Live Preview</h5>
                    <span class="badge bg-success font-ui">Live Rendering</span>
                </div>

                <!-- Preview Card Demo Container -->
                <div class="card p-4 border-warning shadow-sm rounded-4 text-center bg-light mb-4">
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark font-mono">KG-001 &bull; Gauri</span>
                        <h4 class="font-heading mt-2 mb-1">Gou Pooja &amp; Aarti Seva</h4>
                        <p class="text-muted small">Sponsor traditional Veda chanting and Gou Aarti in your name.</p>
                        <h3 class="font-heading text-dark font-mono mb-3">₹ 1,000</h3>
                    </div>

                    <!-- Live Buttons Rendered Here -->
                    <div class="d-flex flex-column gap-2">
                        <button type="button" id="preview-btn-primary" class="btn py-2.5 font-ui fw-bold shadow-sm w-100">
                            <i class="fas fa-heart me-1.5"></i> <span id="preview-lbl-primary">Sponsor Cow</span>
                        </button>
                        <button type="button" id="preview-btn-feed" class="btn py-2.5 font-ui fw-bold shadow-sm w-100">
                            <i class="fas fa-cookie-bite me-1.5"></i> <span id="preview-lbl-feed">Feed Cow</span>
                        </button>
                        <button type="button" id="preview-btn-cart" class="btn py-2.5 font-ui fw-bold shadow-sm w-100">
                            <i class="fas fa-shopping-cart me-1.5"></i> <span id="preview-lbl-cart">Add to Cart</span>
                        </button>
                        <button type="button" id="preview-btn-wa" class="btn py-2.5 font-ui fw-bold shadow-sm w-100">
                            <i class="fab fa-whatsapp me-1.5"></i> <span id="preview-lbl-wa">Sponsor via WhatsApp</span>
                        </button>
                        <button type="button" id="preview-btn-details" class="btn py-2.5 font-ui fw-bold shadow-sm w-100">
                            <i class="fas fa-info-circle me-1.5"></i> <span id="preview-lbl-details">Cow Details</span>
                        </button>
                    </div>
                </div>

                <div class="alert alert-warning small font-ui border-warning">
                    <i class="fas fa-lightbulb text-warning me-1"></i> Changes saved here will instantly update button gradients, labels, and borders across all pages of your user website.
                </div>
            </div>
        </div>

    </div>
</form>

<script>
function updateLivePreview() {
    // 1. Primary Button
    const pLabel = document.getElementById('btn_primary_label').value || 'Sponsor Cow';
    const pStart = document.getElementById('btn_primary_bg_start').value;
    const pEnd = document.getElementById('btn_primary_bg_end').value;
    const pText = document.getElementById('btn_primary_text_color').value;
    const pBorder = document.getElementById('btn_primary_border_color').value;
    const pRadius = document.getElementById('btn_primary_border_radius').value + 'px';

    document.getElementById('btn_primary_bg_start_hex').value = pStart;
    document.getElementById('btn_primary_bg_end_hex').value = pEnd;
    document.getElementById('btn_primary_text_color_hex').value = pText;
    document.getElementById('btn_primary_border_color_hex').value = pBorder;
    document.getElementById('btn_primary_radius_val').textContent = pRadius;

    const btnPrimary = document.getElementById('preview-btn-primary');
    btnPrimary.style.background = `linear-gradient(135deg, ${pStart} 0%, ${pEnd} 100%)`;
    btnPrimary.style.color = pText;
    btnPrimary.style.border = `1.5px solid ${pBorder}`;
    btnPrimary.style.borderRadius = pRadius;
    document.getElementById('preview-lbl-primary').textContent = pLabel;

    // 2. Feed Cow Button
    const fLabel = document.getElementById('btn_feed_label').value || 'Feed Cow';
    const fStart = document.getElementById('btn_feed_bg_start').value;
    const fEnd = document.getElementById('btn_feed_bg_end').value;
    const fText = document.getElementById('btn_feed_text_color').value;
    const fBorder = document.getElementById('btn_feed_border_color').value;
    const fRadius = document.getElementById('btn_feed_border_radius').value + 'px';

    document.getElementById('btn_feed_bg_start_hex').value = fStart;
    document.getElementById('btn_feed_bg_end_hex').value = fEnd;
    document.getElementById('btn_feed_text_color_hex').value = fText;
    document.getElementById('btn_feed_border_color_hex').value = fBorder;
    document.getElementById('btn_feed_radius_val').textContent = fRadius;

    const btnFeed = document.getElementById('preview-btn-feed');
    btnFeed.style.background = `linear-gradient(135deg, ${fStart} 0%, ${fEnd} 100%)`;
    btnFeed.style.color = fText;
    btnFeed.style.border = `1.5px solid ${fBorder}`;
    btnFeed.style.borderRadius = fRadius;
    document.getElementById('preview-lbl-feed').textContent = fLabel;

    // 3. Add to Cart Button
    const cLabel = document.getElementById('btn_cart_label').value || 'Add to Cart';
    const cStart = document.getElementById('btn_cart_bg_start').value;
    const cEnd = document.getElementById('btn_cart_bg_end').value;
    const cText = document.getElementById('btn_cart_text_color').value;
    const cBorder = document.getElementById('btn_cart_border_color').value;
    const cRadius = document.getElementById('btn_cart_border_radius').value + 'px';

    document.getElementById('btn_cart_bg_start_hex').value = cStart;
    document.getElementById('btn_cart_bg_end_hex').value = cEnd;
    document.getElementById('btn_cart_text_color_hex').value = cText;
    document.getElementById('btn_cart_border_color_hex').value = cBorder;
    document.getElementById('btn_cart_radius_val').textContent = cRadius;

    const btnCart = document.getElementById('preview-btn-cart');
    btnCart.style.background = `linear-gradient(135deg, ${cStart} 0%, ${cEnd} 100%)`;
    btnCart.style.color = cText;
    btnCart.style.border = `1.5px solid ${cBorder}`;
    btnCart.style.borderRadius = cRadius;
    document.getElementById('preview-lbl-cart').textContent = cLabel;

    // 4. WhatsApp Button
    const wLabel = document.getElementById('btn_whatsapp_label').value || 'Sponsor via WhatsApp';
    const wStart = document.getElementById('btn_wa_bg_start').value;
    const wEnd = document.getElementById('btn_wa_bg_end').value;
    const wText = document.getElementById('btn_wa_text_color').value;
    const wBorder = document.getElementById('btn_wa_border_color').value;
    const wRadius = document.getElementById('btn_wa_border_radius').value + 'px';

    document.getElementById('btn_wa_bg_start_hex').value = wStart;
    document.getElementById('btn_wa_bg_end_hex').value = wEnd;
    document.getElementById('btn_wa_text_color_hex').value = wText;
    document.getElementById('btn_wa_border_color_hex').value = wBorder;
    document.getElementById('btn_wa_radius_val').textContent = wRadius;

    const btnWa = document.getElementById('preview-btn-wa');
    btnWa.style.background = `linear-gradient(135deg, ${wStart} 0%, ${wEnd} 100%)`;
    btnWa.style.color = wText;
    btnWa.style.border = `1.5px solid ${wBorder}`;
    btnWa.style.borderRadius = wRadius;
    document.getElementById('preview-lbl-wa').textContent = wLabel;

    // 5. Cow Details Button
    const dLabel = document.getElementById('btn_details_label').value || 'Cow Details';
    const dStart = document.getElementById('btn_details_bg_start').value;
    const dEnd = document.getElementById('btn_details_bg_end').value;
    const dText = document.getElementById('btn_details_text_color').value;
    const dBorder = document.getElementById('btn_details_border_color').value;
    const dRadius = document.getElementById('btn_details_border_radius').value + 'px';

    document.getElementById('btn_details_bg_start_hex').value = dStart;
    document.getElementById('btn_details_bg_end_hex').value = dEnd;
    document.getElementById('btn_details_text_color_hex').value = dText;
    document.getElementById('btn_details_border_color_hex').value = dBorder;
    document.getElementById('btn_details_radius_val').textContent = dRadius;

    const btnDetails = document.getElementById('preview-btn-details');
    btnDetails.style.background = `linear-gradient(135deg, ${dStart} 0%, ${dEnd} 100%)`;
    btnDetails.style.color = dText;
    btnDetails.style.border = `1.5px solid ${dBorder}`;
    btnDetails.style.borderRadius = dRadius;
    document.getElementById('preview-lbl-details').textContent = dLabel;
}

function applyPreset(theme) {
    if (theme === 'terracotta') {
        document.getElementById('btn_primary_bg_start').value = '#e67e22';
        document.getElementById('btn_primary_bg_end').value = '#d35400';
        document.getElementById('btn_primary_text_color').value = '#ffffff';
        document.getElementById('btn_primary_border_color').value = '#ffd700';

        document.getElementById('btn_feed_bg_start').value = '#10b981';
        document.getElementById('btn_feed_bg_end').value = '#059669';
        document.getElementById('btn_feed_text_color').value = '#ffffff';
        document.getElementById('btn_feed_border_color').value = '#6ee7b7';

        document.getElementById('btn_cart_bg_start').value = '#fef3c7';
        document.getElementById('btn_cart_bg_end').value = '#fde68a';
        document.getElementById('btn_cart_text_color').value = '#b45309';
        document.getElementById('btn_cart_border_color').value = '#f59e0b';

        document.getElementById('btn_wa_bg_start').value = '#16a34a';
        document.getElementById('btn_wa_bg_end').value = '#15803d';

        document.getElementById('btn_details_bg_start').value = '#1e293b';
        document.getElementById('btn_details_bg_end').value = '#0f172a';
    } else if (theme === 'emerald') {
        document.getElementById('btn_primary_bg_start').value = '#059669';
        document.getElementById('btn_primary_bg_end').value = '#047857';
        document.getElementById('btn_primary_border_color').value = '#a7f3d0';

        document.getElementById('btn_feed_bg_start').value = '#0d9488';
        document.getElementById('btn_feed_bg_end').value = '#0f766e';
    } else if (theme === 'sapphire') {
        document.getElementById('btn_primary_bg_start').value = '#0284c7';
        document.getElementById('btn_primary_bg_end').value = '#0369a1';
        document.getElementById('btn_primary_border_color').value = '#38bdf8';

        document.getElementById('btn_details_bg_start').value = '#334155';
        document.getElementById('btn_details_bg_end').value = '#1e293b';
    } else if (theme === 'saffron') {
        document.getElementById('btn_primary_bg_start').value = '#f59e0b';
        document.getElementById('btn_primary_bg_end').value = '#d97706';
        document.getElementById('btn_primary_border_color').value = '#fef08a';
    }
    updateLivePreview();
}

document.addEventListener("DOMContentLoaded", function() {
    updateLivePreview();
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
