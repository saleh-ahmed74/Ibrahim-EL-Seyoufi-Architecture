'use client';
import Link from 'next/link';
import { ReactNode, useEffect, useState } from 'react';

const text = {
    en: { home: 'Home', projects: 'Projects', about: 'About', theme: 'Theme', language: 'العربية' },
    ar: { home: 'الرئيسية', projects: 'المشاريع', about: 'من أنا', theme: 'المظهر', language: 'English' },
};

type SocialLinks = {
    instagram_url?: string;
    facebook_url?: string;
    linkedin_url?: string;
    behance_url?: string;
    pinterest_url?: string;
    youtube_url?: string;
    whatsapp_number?: string;
    whatsapp_message?: string;
};

function getInitialLang(): 'en' | 'ar' {
    return 'en';
}

function getInitialDark(): boolean {
    return false;
}

export default function SiteShell({
    children,
    name = 'Architecture',
    social,
}: {
    children: ReactNode;
    name?: string;
    social?: SocialLinks;
}) {
    const [lang, setLang] = useState<'en' | 'ar'>(getInitialLang);
    const [dark, setDark] = useState<boolean>(getInitialDark);
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        try {
            const savedLang = localStorage.getItem('site_lang');
            const savedTheme = localStorage.getItem('site_theme');
            if (savedLang === 'ar') setLang('ar');
            if (savedTheme === 'dark') setDark(true);
        } catch (e) {}
        setMounted(true);
    }, []);

    useEffect(() => {
        if (!mounted) return;
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.dataset.theme = dark ? 'dark' : 'light';
        try {
            localStorage.setItem('site_lang', lang);
            localStorage.setItem('site_theme', dark ? 'dark' : 'light');
        } catch (e) {}
    }, [lang, dark, mounted]);

    const t = text[lang];

    const iconLinks = social
        ? [
              { key: 'instagram', url: social.instagram_url, icon: <InstagramIcon /> },
              { key: 'facebook', url: social.facebook_url, icon: <FacebookIcon /> },
              { key: 'linkedin', url: social.linkedin_url, icon: <LinkedinIcon /> },
              { key: 'behance', url: social.behance_url, icon: <BehanceIcon /> },
              { key: 'pinterest', url: social.pinterest_url, icon: <PinterestIcon /> },
              { key: 'youtube', url: social.youtube_url, icon: <YoutubeIcon /> },
          ].filter((s) => s.url)
        : [];

    const whats = social?.whatsapp_number
        ? `https://wa.me/${social.whatsapp_number.replace(/\D/g, '')}${
              social.whatsapp_message ? `?text=${encodeURIComponent(social.whatsapp_message)}` : ''
          }`
        : null;

    return (
        <>
            <header className="nav nav-sticky">
                <Link href="/" className="wordmark">{name}</Link>
                <nav>
                    <Link href="/">{t.home}</Link>
                    <Link href="/projects">{t.projects}</Link>
                    <Link href="/about">{t.about}</Link>
                </nav>
                <div className="nav-actions">
                    {iconLinks.map((s) => (
                        <a key={s.key} href={s.url} target="_blank" rel="noopener noreferrer" aria-label={s.key}>
                            {s.icon}
                        </a>
                    ))}
                    <button onClick={() => setLang(lang === 'en' ? 'ar' : 'en')}>{t.language}</button>
                    <button aria-label={t.theme} onClick={() => setDark(!dark)} suppressHydrationWarning>
                        {mounted && dark ? '☀' : '◐'}
                    </button>
                </div>
            </header>

            <main>{children}</main>

            {whats && (
                <a className="whatsapp-fab" href={whats} target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                    <WhatsappIcon />
                </a>
            )}

            <footer>
                <span>{name}</span>
                <span>© {new Date().getFullYear()}</span>
            </footer>
        </>
    );
}

function InstagramIcon() {
    return (
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" strokeWidth="1.8" />
            <circle cx="12" cy="12" r="4.2" stroke="currentColor" strokeWidth="1.8" />
            <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" />
        </svg>
    );
}
function FacebookIcon() {
    return (
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <path d="M15 8.5h2.5V5h-2.5c-2.2 0-4 1.8-4 4v2H9v3.5h2v6.5h3.5v-6.5h2.5l.5-3.5h-3V9c0-.3.2-.5.5-.5z" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round" />
        </svg>
    );
}
function LinkedinIcon() {
    return (
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="2" width="20" height="20" rx="3" stroke="currentColor" strokeWidth="1.8" />
            <circle cx="7" cy="8" r="1.2" fill="currentColor" />
            <path d="M7 11v7M12 11v7M12 14c0-1.8 1.2-3 2.7-3S17 12.2 17 14v4" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />
        </svg>
    );
}
function BehanceIcon() {
    return (
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <text x="2" y="17" fontSize="14" fontFamily="Manrope, sans-serif" fill="currentColor">Be</text>
        </svg>
    );
}
function PinterestIcon() {
    return (
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.8" />
            <path d="M9.5 18c.5-2 1.5-6 1.5-6m0 0c-.5-1.5.3-3.2 2-3.2 1.5 0 2.2 1.1 2.2 2.4 0 1.5-1 3.7-1.5 4.8-.3.9.2 1.6 1.1 1.6 1.4 0 2.5-1.7 2.5-4 0-2.1-1.6-3.9-4-3.9-2.8 0-4.4 2-4.4 4.2 0 .8.2 1.4.6 1.8" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" />
        </svg>
    );
}
function YoutubeIcon() {
    return (
        <svg width="16" height="15" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="4" stroke="currentColor" strokeWidth="1.8" />
            <path d="M10 9.5l6 3-6 3v-6z" fill="currentColor" />
        </svg>
    );
}
function WhatsappIcon() {
    return (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2a10 10 0 00-8.5 15.2L2 22l4.9-1.4A10 10 0 1012 2z" stroke="currentColor" strokeWidth="1.6" />
            <path d="M8.5 8.7c.2-.5.5-.5.8-.5h.5c.2 0 .4 0 .6.4.2.5.6 1.5.7 1.6.1.1.1.3 0 .4-.1.2-.2.3-.3.5-.2.2-.3.3-.1.6.2.3.9 1.4 2 2.3 1.3 1.1 2 1.3 2.3 1.2.3-.1.5-.1.7-.4.2-.2.4-.5.6-.5.2 0 1.4.6 1.6.7.2.1.4.2.4.3.1.4-.1 1-.5 1.4-.4.5-1.3.9-2.4.7-1-.2-3-1.1-4.4-2.6-1.4-1.5-2-2.9-2.2-3.5-.2-.6-.3-1.4.1-1.9z" fill="currentColor" />
        </svg>
    );
}