/**
 * KAMADENU GOUSHALA - DYNAMIC LANGUAGE SWITCHER
 */

function setLanguage(lang) {
    fetch('/Kamadenu/api/language.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lang: lang })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', lang);
            window.location.href = url.toString();
        }
    })
    .catch(err => {
        console.error("Language switch error:", err);
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    });
}
