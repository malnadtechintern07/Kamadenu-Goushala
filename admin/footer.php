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

    // Dynamic toggle for WhatsApp fields in Admin forms
    const contactMethodSelect = document.querySelector('select[name="contact_method"]');
    if (contactMethodSelect) {
        const updateWaFieldsVisibility = () => {
            const val = contactMethodSelect.value;
            const waPhoneSelect = document.querySelector('select[name="whatsapp_number_id"]');
            const waMessageInput = document.querySelector('input[name="whatsapp_message"]');
            
            if (waPhoneSelect && waMessageInput) {
                const waPhoneCol = waPhoneSelect.closest('[class*="col-"]');
                const waMessageCol = waMessageInput.closest('[class*="col-"]');
                
                if (val === 'website') {
                    // Hide them smoothly
                    if (waPhoneCol) {
                        waPhoneCol.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        waPhoneCol.style.opacity = '0';
                        waPhoneCol.style.transform = 'translateY(-10px)';
                        waPhoneCol.style.pointerEvents = 'none';
                        setTimeout(() => {
                            if (contactMethodSelect.value === 'website') {
                                waPhoneCol.style.display = 'none';
                            }
                        }, 300);
                    }
                    if (waMessageCol) {
                        waMessageCol.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        waMessageCol.style.opacity = '0';
                        waMessageCol.style.transform = 'translateY(-10px)';
                        waMessageCol.style.pointerEvents = 'none';
                        setTimeout(() => {
                            if (contactMethodSelect.value === 'website') {
                                waMessageCol.style.display = 'none';
                            }
                        }, 300);
                    }
                } else {
                    // Show them
                    if (waPhoneCol) {
                        waPhoneCol.style.display = '';
                        setTimeout(() => {
                            waPhoneCol.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            waPhoneCol.style.opacity = '1';
                            waPhoneCol.style.transform = 'translateY(0)';
                            waPhoneCol.style.pointerEvents = 'auto';
                        }, 10);
                    }
                    if (waMessageCol) {
                        waMessageCol.style.display = '';
                        setTimeout(() => {
                            waMessageCol.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            waMessageCol.style.opacity = '1';
                            waMessageCol.style.transform = 'translateY(0)';
                            waMessageCol.style.pointerEvents = 'auto';
                        }, 10);
                    }
                }
            }
        };
        
        contactMethodSelect.addEventListener('change', updateWaFieldsVisibility);
        
        // Run once on load to establish initial state
        const waPhoneSelect = document.querySelector('select[name="whatsapp_number_id"]');
        const waMessageInput = document.querySelector('input[name="whatsapp_message"]');
        if (waPhoneSelect && waMessageInput) {
            const val = contactMethodSelect.value;
            const waPhoneCol = waPhoneSelect.closest('[class*="col-"]');
            const waMessageCol = waMessageInput.closest('[class*="col-"]');
            if (val === 'website') {
                if (waPhoneCol) {
                    waPhoneCol.style.display = 'none';
                    waPhoneCol.style.opacity = '0';
                    waPhoneCol.style.transform = 'translateY(-10px)';
                }
                if (waMessageCol) {
                    waMessageCol.style.display = 'none';
                    waMessageCol.style.opacity = '0';
                    waMessageCol.style.transform = 'translateY(-10px)';
                }
            }
        }
    }
});
</script>
</body>
</html>
