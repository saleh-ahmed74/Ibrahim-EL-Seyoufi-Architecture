'use client';
import { useState } from 'react';
import { asset } from '@/lib/api';

type GalleryImage = { url: string; alt_text?: string | null };

export default function ProjectGallery({
    images,
    title,
}: {
    images: GalleryImage[];
    title: string;
}) {
    const [active, setActive] = useState(0);

    if (!images.length) return null;

    const go = (dir: 1 | -1) => {
        setActive((prev) => (prev + dir + images.length) % images.length);
    };

    return (
        <div className="project-gallery">
            <div className="gallery-main">
                <button
                    className="gallery-nav prev"
                    onClick={() => go(-1)}
                    aria-label="Previous image"
                >
                    ‹
                </button>
                <img
                    src={asset(images[active].url)}
                    alt={images[active].alt_text || `${title} ${active + 1}`}
                />
                <button
                    className="gallery-nav next"
                    onClick={() => go(1)}
                    aria-label="Next image"
                >
                    ›
                </button>
            </div>

            <div className="gallery-thumbs">
                {images.map((img, i) => (
                    <button
                        key={i}
                        className={i === active ? 'thumb active' : 'thumb'}
                        onClick={() => setActive(i)}
                        aria-label={`View image ${i + 1}`}
                    >
                        <img src={asset(img.url)} alt="" />
                    </button>
                ))}
            </div>
        </div>
    );
}