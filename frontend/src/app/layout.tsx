import type { Metadata } from 'next'; import './globals.css';
export const metadata:
    Metadata = {
    title: 'Architecture Portfolio',
    description: 'Architect portfolio'
};
export default function RootLayout({ children }:
    Readonly<{ children: React.ReactNode }>) {
    const setInitialThemeAndLang =
        `(function(){try{var l=localStorage.getItem('site_lang')||'en';
     document.documentElement.lang = l; document.documentElement.dir = l==='ar' ? 'rtl' : 'ltr'; 
     var t=localStorage.getItem('site_theme'); if(t) document.documentElement.dataset.theme = t;}catch(e){} })();`;
    return <html lang="en" dir="ltr" data-theme="light" suppressHydrationWarning><head>
        <script dangerouslySetInnerHTML={{ __html: setInitialThemeAndLang }} /></head>
        <body>{children}</body></html>
}
