/**
 * KAMADENU GOUSHALA - API CLIENT WRAPPER
 */

const API = {
    async get(endpoint, params = {}) {
        const url = new URL(`/Kamadhenu-goushala/api/${endpoint}.php`, window.location.origin);
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        const res = await fetch(url);
        return res.json();
    },

    async post(endpoint, data = {}) {
        const res = await fetch(`/Kamadhenu-goushala/api/${endpoint}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return res.json();
    }
};
