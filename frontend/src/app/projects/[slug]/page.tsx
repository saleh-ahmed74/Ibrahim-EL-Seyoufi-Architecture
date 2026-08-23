import { notFound } from 'next/navigation';
import SiteShell from '@/components/site-shell';
import { asset, getProject, getSettings } from '@/lib/api';
import ProjectGallery from '@/components/project-gallery';

export const dynamic = 'force-dynamic';
export default async function ProjectDetails({ params }: { params: Promise<{ slug: string }> }) {
    const { slug } = await params; try {
        const [settings, p] =
            await Promise.all([getSettings(), getProject(slug)]);
        return <SiteShell name={settings.site_name}><article className="project-detail"><header>
            <span className="eyebrow">{p.category?.name || 'Architecture'}</span>
            <h1>{p.title}</h1><p className="detail-meta">{p.location} · {p.year}</p>
        </header>{p.cover_image && <img className="cover" src={asset(p.cover_image.url)}
            alt={p.cover_image.alt_text || p.title} />}
            <div className="description">{p.description}</div>
            <div className="gallery">{p.images?.map((image, index) =>
                <img key={index} src={asset(image.url)}
                    alt={image.alt_text || `${p.title} ${index + 1}`} />)}</div>
        </article></SiteShell>
    } catch { notFound(); }
}
