/**
 * KAMADENU GOUSHALA - REAL-TIME DATA SYNCHRONIZATION
 */

document.addEventListener('DOMContentLoaded', () => {
    // Start AJAX Polling interval for live sync (every 10 seconds)
    setInterval(pollLiveUpdates, 10000);
});

function pollLiveUpdates() {
    fetch('/Kamadhenu-goushala/api/realtime.php')
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                updateLiveUI(res.data);
            }
        })
        .catch(err => {
            // Quiet fail
        });
}

function updateLiveUI(data) {
    // 1. Update Donation Total Counters
    const donationElems = document.querySelectorAll('.live-total-donations');
    donationElems.forEach(el => {
        if (data.total_donations) {
            el.textContent = '₹' + Number(data.total_donations).toLocaleString('en-IN');
        }
    });

    // 2. Update Emergency Campaign Raised Amounts
    if (data.campaigns) {
        data.campaigns.forEach(c => {
            const raisedEl = document.getElementById(`campaign-raised-${c.id}`);
            const progressEl = document.getElementById(`campaign-progress-${c.id}`);
            if (raisedEl) {
                raisedEl.textContent = '₹' + Number(c.raised_amount).toLocaleString('en-IN');
            }
            if (progressEl) {
                const pct = Math.min(100, Math.round((c.raised_amount / c.target_amount) * 100));
                progressEl.style.width = pct + '%';
                progressEl.textContent = pct + '%';
            }
        });
    }

    // 3. Update Unread Notifications Badge
    if (data.unread_notifications !== undefined) {
        const notifBadges = document.querySelectorAll('.notif-badge');
        notifBadges.forEach(b => {
            b.textContent = data.unread_notifications;
            b.style.display = data.unread_notifications > 0 ? 'inline-block' : 'none';
        });
    }
}
