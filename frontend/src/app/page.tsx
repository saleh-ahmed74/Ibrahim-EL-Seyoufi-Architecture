import Link from 'next/link';
import ProjectCard from '@/components/project-card';
import SiteShell from '@/components/site-shell';
import CategorySelect from '@/components/category-select';
import HeroSlideshow from '@/components/hero-slideshow';
import { getProjects, getSettings, getCategories, asset } from '@/lib/api';

export const dynamic = 'force-dynamic';

export default async function Home({
    searchParams,
}: {
    searchParams: Promise<{ category?: string }>;
}) {
    const { category } = await searchParams;
    const [settings, projects, categories] = await Promise.all([
        getSettings(),
        getProjects(category),
        getCategories(),
    ]);
    const whats = `https://wa.me/${settings.whatsapp_number?.replace(/\D/g, '') || ''}?
    text=${encodeURIComponent(settings.whatsapp_message || '')}`;
    const nameParts = (settings.architect_name || '').split(/\s+/);
    const firstName = nameParts.shift() || '';
    const restName = nameParts.join(' ');

    return (
        <SiteShell name={settings.site_name} social={settings}>
            <section className="hero">
                <div className="hero-header">
                    {asset(settings.profile_image) ? (
                        <img className="profile-image" src={asset(settings.profile_image)} alt={settings.architect_name} />
                    ) : null}
                    <div className="hero-name">
                        <span className="eyebrow">Independent practice · Cairo</span>
                        <h1>
                            <span className="given">{firstName}</span>
                            {restName ? <span className="surname">{restName}</span> : null}
                        </h1>
                        <p className="title">{settings.professional_title}</p>
                        <p className="intro">{settings.short_bio}</p>
                        <div className="cta-row">
                            <Link className="button" href="/projects">View projects</Link>
                            <a className="button secondary" href={whats}>WhatsApp ↗</a>
                        </div>
                    </div>
                </div>
                <HeroSlideshow
                    images={projects
                        .slice(0, 6)
                        .filter((p) => p.cover_image)
                        .map((p) => ({ url: p.cover_image!.url, alt: p.cover_image!.alt_text || p.title }))}
                />
            </section>

            <section className="section">
                <div className="section-head">
                    <span className="eyebrow">Selected work</span>
                    <Link href="/projects">All projects →</Link>
                </div>

               <CategorySelect categories={categories} current={category ?? ''} basePath="/projects" />

                <div className="projects-grid">
                    {projects.slice(0, 6).map((p) => (
                        <ProjectCard key={p.id} project={p} />
                    ))}
                </div>
            </section>

            <section className="about-band">
                <span className="eyebrow">Practice</span>
                <p>{settings.bio}</p>
                <Link className="text-link" href="/about">More about the studio →</Link>
            </section>
        </SiteShell>
    );
}