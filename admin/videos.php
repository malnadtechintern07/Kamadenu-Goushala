<?php
require_once __DIR__ . '/header.php';

$videos = $pdo->query("SELECT * FROM videos ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0"><i class="fab fa-youtube text-danger me-2"></i> Program Videos</h3>
    <a href="/Kamadhenu-goushala/admin/video-add.php" class="btn btn-kamadenu-primary font-ui fw-bold"><i class="fas fa-plus me-1"></i> Add New Video</a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">Program video created successfully!</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Program video and link updated permanently in MySQL.</div>
<?php endif; ?>

<div class="kamadenu-card p-4">
    <?php if (empty($videos)): ?>
        <p class="text-muted text-center py-4">No program videos added yet. Click "Add New Video" to begin.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 180px;">Preview</th>
                        <th>Video Title</th>
                        <th>Description</th>
                        <th>YouTube Link</th>
                        <th>Added Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $vid): ?>
                        <?php 
                            // Extract video ID from URL
                            $video_id = '';
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $vid['youtube_url'], $match)) {
                                $video_id = $match[1];
                            } elseif (preg_match('%youtube\.com/shorts/([^"&?/ ]{11})%i', $vid['youtube_url'], $match)) {
                                $video_id = $match[1];
                            } elseif (preg_match('%youtube\.com/embed/([^"&?/ ]{11})%i', $vid['youtube_url'], $match)) {
                                $video_id = $match[1];
                            }
                        ?>
                        <tr>
                            <td>
                                <?php if ($video_id): ?>
                                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm" style="max-width: 150px;">
                                        <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>" title="Preview" allowfullscreen style="border: 0;"></iframe>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-danger">Invalid URL</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo e($vid['title']); ?></strong></td>
                            <td class="small text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($vid['description']); ?></td>
                            <td><a href="<?php echo e($vid['youtube_url']); ?>" target="_blank" class="small text-decoration-none font-mono text-warning"><i class="fab fa-youtube me-1"></i> View Video</a></td>
                            <td class="font-mono small"><?php echo date('M d, Y', strtotime($vid['created_at'])); ?></td>
                            <td>
                                <a href="/Kamadhenu-goushala/admin/video-edit.php?id=<?php echo $vid['id']; ?>" class="btn btn-sm btn-outline-warning font-ui fw-bold"><i class="fas fa-edit me-1"></i> Edit</a>
                                <button onclick="deleteAdminItem('videos', <?php echo $vid['id']; ?>)" class="btn btn-sm btn-outline-danger font-ui fw-bold ms-1"><i class="fas fa-trash me-1"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
