<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q_en = trim($_POST['quote_en']);
    $q_kn = trim($_POST['quote_kn']);
    $q_hi = trim($_POST['quote_hi']);
    $source = trim($_POST['source']);

    $stmt = $pdo->prepare("INSERT INTO quotes (quote_en, quote_kn, quote_hi, source, date_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$q_en, $q_kn, $q_hi, $source, date('Y-m-d')]);

    log_audit($pdo, 'Add Devotional Quote', 'quotes', $pdo->lastInsertId());
    header("Location: /Kamadhenu-goushala/admin/quotes.php?saved=1");
    exit;
}

$quotes = $pdo->query("SELECT * FROM quotes ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-om text-warning me-2"></i> Daily Devotional Scripture Quotes</h3>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Add Devotional Quote</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Quote (English)</label>
                    <textarea name="quote_en" rows="2" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Quote (Kannada)</label>
                    <textarea name="quote_kn" rows="2" class="form-control kn-text" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label font-ui small fw-bold">Quote (Hindi)</label>
                    <textarea name="quote_hi" rows="2" class="form-control" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label font-ui small fw-bold">Scripture Source</label>
                    <input type="text" name="source" class="form-control" value="Vedic Scriptures">
                </div>
                <button type="submit" class="btn btn-kamadenu-primary w-100 font-ui fw-bold">Add Quote</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="kamadenu-card p-4">
            <h4 class="font-heading mb-3">Quote Repository</h4>
            <?php foreach ($quotes as $q): ?>
                <div class="border-bottom pb-3 mb-3">
                    <span class="badge bg-warning text-dark font-ui"><?php echo e($q['source']); ?></span>
                    <blockquote class="blockquote fs-6 mt-2 mb-1">"<?php echo e($q['quote_en']); ?>"</blockquote>
                    <p class="kn-text text-warning small mb-0">"<?php echo e($q['quote_kn']); ?>"</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
