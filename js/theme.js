/**
 * KAMADENU GOUSHALA - DYNAMIC THEME SWITCHER
 */

function setTheme(theme) {
    const themeLink = document.getElementById('theme-stylesheet');
    if (themeLink) {
        themeLink.href = `/Kamadenu/css/${theme}.css`;
    }
    
    fetch('/Kamadenu/api/theme.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ theme: theme })
    })
    .then(res => res.json())
    .then(data => {
        document.body.setAttribute('data-theme', theme);
        showToast(`Theme updated to ${theme.toUpperCase()}`, 'info');
    })
    .catch(err => {
        console.error("Theme update error:", err);
    });
}
