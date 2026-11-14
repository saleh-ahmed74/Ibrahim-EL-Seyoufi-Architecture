const applyPreferences = () => {
    try {
        const language = localStorage.getItem('site_lang') || 'en';
        const theme = localStorage.getItem('site_theme') || 'light';

        document.documentElement.lang = language;
        document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.dataset.theme = theme;

        document.querySelectorAll('[data-en][data-ar]').forEach(element => {
            element.textContent = element.dataset[language];
        });

        const languageToggle = document.querySelector('[data-language-toggle]');
        if (languageToggle) languageToggle.textContent = language === 'en' ? 'العربية' : 'English';

        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) themeToggle.textContent = theme === 'dark' ? '☀' : '◐';
    } catch {}
};

document.addEventListener('DOMContentLoaded', () => {
    applyPreferences();

    document.querySelector('[data-language-toggle]')?.addEventListener('click', () => {
        try {
            const language = localStorage.getItem('site_lang') === 'ar' ? 'en' : 'ar';
            localStorage.setItem('site_lang', language);
            applyPreferences();
        } catch {}
    });

    document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
        try {
            const theme = localStorage.getItem('site_theme') === 'dark' ? 'light' : 'dark';
            localStorage.setItem('site_theme', theme);
            applyPreferences();
        } catch {}
    });

    if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
        navigator.serviceWorker.register('/service-worker.js', { scope: '/' }).catch(() => {});
    }
});
