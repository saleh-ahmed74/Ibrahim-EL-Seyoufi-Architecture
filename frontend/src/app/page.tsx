import Link from 'next/link';
import ProjectCard from '@/components/project-card';
import SiteShell from '@/components/site-shell';
import { getProjects, getSettings, asset } from '@/lib/api';
export const dynamic = 'force-dynamic';
export default async function Home() {
    const [settings, projects] = await Promise.all([getSettings(), getProjects()]);
    const whats = `https://wa.me/${settings.whatsapp_number?.replace(/\D/g, '') || ''}?
    text=${encodeURIComponent(settings.whatsapp_message || '')}`;
    const nameParts = (settings.architect_name || '').split(/\s+/);
    const firstName = nameParts.shift() || '';
    const restName = nameParts.join(' ');

    return <SiteShell name={settings.site_name}><section className="hero">
        <div className="hero-header">
            {asset(settings.profile_image) ? <img className="profile-image"
                src={asset(settings.profile_image)} alt={settings.architect_name} /> : null}
            <div className="hero-name">
                <span className="eyebrow">Independent practice · Cairo</span>
                <h1><span className="given">{firstName}</span>{restName ? <span className="surname">{restName}</span> : null}</h1>
                <p className="title">{settings.professional_title}</p>
                <p className="intro">{settings.short_bio}</p>
                <div className="cta-row"><Link className="button" href="/projects">View projects</Link>
                    <a className="button secondary" href={whats}>WhatsApp ↗</a></div>
            </div>
        </div>
        {asset(settings.hero_image) && <img className="hero-image" src={asset(settings.hero_image)} alt="Architectural work" />}
    </section>
        <section className="section"><div className="section-head">
            <span className="eyebrow">Selected work</span>
            <Link href="/projects">All projects →</Link></div>
            <div className="projects-grid">{projects.slice(0, 6).map(p =>
                <ProjectCard key={p.id} project={p} />)}</div></section>
        <section className="about-band"><span className="eyebrow">Practice</span>
            <p>{settings.bio}</p><Link className="text-link"
                href="/about">More about the studio →</Link></section></SiteShell>
}
