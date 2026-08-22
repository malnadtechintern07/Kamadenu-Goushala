<?php
require_once __DIR__ . '/header.php';

$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i> Trust Events & Celebrations</h3>
    <a href="/Kamadenu/admin/event-add.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add New Event</a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">Trust event created successfully!</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Trust event and photo updated permanently in MySQL.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Event Title</th>
                    <th>Event Date</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><img src="<?php echo img_url($ev['photo']); ?>" width="60" height="45" class="rounded object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'"></td>
                        <td>
                            <strong><?php echo e($ev['title']); ?></strong>
                            <?php if ($ev['title_kn']) echo "<small class='kn-text text-warning d-block'>({$ev['title_kn']})</small>"; ?>
                        </td>
                        <td class="font-mono"><?php echo e($ev['event_date']); ?></td>
                        <td class="small"><?php echo e($ev['venue']); ?></td>
                        <td><span class="badge <?php echo $ev['status'] === 'Upcoming' ? 'bg-warning text-dark' : 'bg-success'; ?>"><?php echo e($ev['status']); ?></span></td>
                        <td>
                            <a href="/Kamadenu/admin/event-edit.php?id=<?php echo $ev['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit & Update Photo</a>
                            <button onclick="deleteAdminItem('events', <?php echo $ev['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
