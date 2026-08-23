'use client';
import { useRouter } from 'next/navigation';
import { useEffect, useRef, useState } from 'react';

export default function CategorySelect({
    categories,
    current,
    basePath = '/projects',
}: {
    categories: { name: string; slug: string }[];
    current: string;
    basePath?: string;
}) {
    const router = useRouter();
    const [open, setOpen] = useState(false);
    const ref = useRef<HTMLDivElement>(null);

    const options = [{ name: 'All work', slug: '' }, ...categories];
    const activeLabel = options.find((o) => o.slug === current)?.name || 'All work';

    useEffect(() => {
        const onClick = (e: MouseEvent) => {
            if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
        };
        document.addEventListener('mousedown', onClick);
        return () => document.removeEventListener('mousedown', onClick);
    }, []);

    const select = (slug: string) => {
        setOpen(false);
        router.push(slug ? `${basePath}?category=${slug}` : basePath);
    };

    return (
        <div className="filter-bar" ref={ref}>
            <span className="eyebrow">Filter</span>
            <div className="filter-dropdown">
                <button
                    className="filter-trigger"
                    onClick={() => setOpen((v) => !v)}
                    aria-expanded={open}
                >
                    <span>{activeLabel}</span>
                    <svg
                        className={open ? 'chevron open' : 'chevron'}
                        width="9" height="6" viewBox="0 0 9 6" fill="none"
                    >
                        <path d="M1 1L4.5 5L8 1" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                </button>

                {open && (
                    <ul className="filter-menu">
                        {options.map((o) => (
                            <li key={o.slug}>
                                <button
                                    className={o.slug === current ? 'filter-option active' : 'filter-option'}
                                    onClick={() => select(o.slug)}
                                >
                                    {o.name}
                                </button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
}