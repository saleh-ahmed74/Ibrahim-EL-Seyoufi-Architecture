'use client';
import { useEffect, useState, useCallback } from 'react';
import { asset } from '@/lib/api';

type SlideImage = { url: string; alt?: string };

export default function HeroSlideshow({ images }: { images: SlideImage[] }) {
    const [active, setActive] = useState(0);

    const next = useCallback(() => {
        setActive((prev) => (prev + 1) % images.length);
    }, [images.length]);

    const prev = useCallback(() => {
        setActive((p) => (p - 1 + images.length) % images.length);
    }, [images.length]);

    useEffect(() => {
        if (images.length <= 1) return;
        const timer = setInterval(next, 2600);
        return () => clearInterval(timer);
    }, [images.length, next, active]);

    if (!images.length) return null;

    return (
        <div className="hero-slideshow">
            {images.map((img, i) => (
                <img
                    key={i}
                    src={asset(img.url)}
                    alt={img.alt || 'Architectural work'}
                    className={i === active ? 'slide active' : 'slide'}
                />
            ))}

            {images.length > 1 && (
                <div className="slideshow-controls">
                    <button aria-label="Previous slide" onClick={prev}>‹</button>
                    <button aria-label="Next slide" onClick={next}>›</button>
                </div>
            )}
        </div>
    );
}