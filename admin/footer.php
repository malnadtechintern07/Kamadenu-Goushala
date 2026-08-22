    </div> <!-- end flex-grow-1 -->
</div> <!-- end d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/Kamadenu/js/main.js"></script>
<script>
function deleteAdminItem(tableName, itemId, reloadOrRedirectUrl = '') {
    if (!confirm('Are you sure you want to delete this item? This action is irreversible.')) {
        return;
    }
    
    fetch('/Kamadenu/api/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ table: tableName, id: itemId })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => {
                if (reloadOrRedirectUrl) {
                    window.location.href = reloadOrRedirectUrl;
                } else {
                    window.location.reload();
                }
            }, 800);
        } else {
            showToast(res.message, 'danger');
        }
    })
    .catch(err => {
        showToast('An error occurred. Please try again.', 'danger');
    });
}

// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar = document.querySelector('.admin-sidebar-nav');
    const overlay = document.getElementById('sidebar-overlay-el');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show-sidebar');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show-sidebar');
            overlay.classList.remove('active');
        });
    }
});
</script>
</body>
</html>
