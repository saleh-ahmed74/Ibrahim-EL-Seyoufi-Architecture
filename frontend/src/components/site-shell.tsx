'use client';
import Link from 'next/link';
import { ReactNode, useEffect, useState } from 'react';

const text = {
    en: {
        home: 'Home', projects: 'Projects', about: 'About',
        whatsapp: 'WhatsApp', theme: 'Theme',
        language: 'العربية'
    }, ar: {
        home: 'الرئيسية',
        projects: 'المشاريع', about: 'من أنا', whatsapp: 'واتساب',
        theme: 'المظهر', language: 'English'
    }
};

type SocialLinks = {
    instagram_url?: string;
    facebook_url?: string;
    linkedin_url?: string;
    youtube_url?: string;
    pinterest_url?: string;
};

export default function SiteShell({
    children,
    name = 'Architecture',
    social,
}: {
    children: ReactNode;
    name?: string;
    social?: SocialLinks;
}) {
    const [lang, setLang] = useState<'en' | 'ar'>('en');
    const [dark, setDark] = useState(false);

    useEffect(() => {
        try {
            const l = localStorage.getItem('site_lang');
            const t = localStorage.getItem('site_theme');
            if (l === 'ar' || l === 'en') setLang(l as 'en' | 'ar');
            if (t) setDark(t === 'dark');
        } catch (e) { }
    }, []);

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.dataset.theme = dark ? 'dark' : 'light';
        try { localStorage.setItem('site_lang', lang); localStorage.setItem('site_theme', dark ? 'dark' : 'light'); } catch (e) { }
    }, [lang, dark]);

    const t = text[lang];

    const socialLinks = social
        ? [
              { key: 'instagram', url: social.instagram_url, label: 'Instagram' },
              { key: 'facebook', url: social.facebook_url, label: 'Facebook' },
              { key: 'linkedin', url: social.linkedin_url, label: 'LinkedIn' },
              { key: 'youtube', url: social.youtube_url, label: 'youtube' },
              { key: 'pinterest', url: social.pinterest_url, label: 'Pinterest' },
          ].filter((s) => s.url)
        : [];

    return <>
        <header className="nav nav-sticky"><Link href="/" className="wordmark">{name}</Link><nav>
            <Link href="/">{t.home}</Link><Link href="/projects">{t.projects}</Link>
            <Link href="/about">{t.about}</Link></nav><div className="nav-actions">
                {socialLinks.map((s) => (
                    <a key={s.key} href={s.url} target="_blank" rel="noopener noreferrer">
                        {s.label}
                    </a>
                ))}
                <button onClick={() => setLang(lang === 'en' ? 'ar' : 'en')}>
                    {t.language}</button>
                <button aria-label={t.theme} onClick={() => setDark(!dark)}>
                    {dark ? '☀' : '◐'}</button>
            </div></header><main>{children}</main><footer><span>{name}</span>
            <span>© {new Date().getFullYear()}</span></footer></>
}