const applyPreferences = () => {
    try {
        const language = localStorage.getItem('site_lang') || 'en';
        const themeToggle = document.querySelector('[data-theme-toggle]');
        const themeStorageKey = themeToggle?.dataset.themeStorage || 'site_theme';
        const theme = localStorage.getItem(themeStorageKey) || themeToggle?.dataset.themeDefault || 'dark';

        document.documentElement.lang = language;
        document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.dataset.theme = theme;

        document.querySelectorAll('[data-en][data-ar]').forEach(element => {
            element.textContent = element.dataset[language];
        });

        const languageToggle = document.querySelector('[data-language-toggle]');
        if (languageToggle) languageToggle.textContent = language === 'en'
            ? (languageToggle.dataset.enLabel || 'العربية')
            : (languageToggle.dataset.arLabel || 'English');

        if (themeToggle) {
            const lightLabel = themeToggle.dataset[language === 'ar' ? 'lightLabelAr' : 'lightLabelEn'] || themeToggle.dataset.lightLabel || 'Dark mode';
            const darkLabel = themeToggle.dataset[language === 'ar' ? 'darkLabelAr' : 'darkLabelEn'] || themeToggle.dataset.darkLabel || 'Light mode';
            themeToggle.textContent = theme === 'dark' ? darkLabel : lightLabel;
        }

        document.querySelectorAll('[data-en-aria-label][data-ar-aria-label]').forEach(element => {
            element.setAttribute('aria-label', language === 'ar' ? element.dataset.arAriaLabel : element.dataset.enAriaLabel);
        });

        document.querySelectorAll('[data-en-open-label][data-ar-open-label][data-en-close-label][data-ar-close-label]').forEach(element => {
            element.dataset.openLabel = language === 'ar' ? element.dataset.arOpenLabel : element.dataset.enOpenLabel;
            element.dataset.closeLabel = language === 'ar' ? element.dataset.arCloseLabel : element.dataset.enCloseLabel;
        });
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
            const themeToggle = document.querySelector('[data-theme-toggle]');
            const themeStorageKey = themeToggle?.dataset.themeStorage || 'site_theme';
            const currentTheme = localStorage.getItem(themeStorageKey) || themeToggle?.dataset.themeDefault || 'dark';
            const theme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem(themeStorageKey, theme);
            applyPreferences();
        } catch {}
    });

    if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
        navigator.serviceWorker.register('/service-worker.js', { scope: '/' }).catch(() => {});
    }
});
