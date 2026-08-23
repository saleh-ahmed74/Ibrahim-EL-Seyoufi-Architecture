import ProjectCard from '@/components/project-card';
import SiteShell from '@/components/site-shell';
import { getProjects, getSettings } from '@/lib/api';
export const dynamic = 'force-dynamic';
export default async function ProjectsPage() {
    const [settings, projects] = await Promise.all([getSettings(), getProjects()]);
    return <SiteShell name={settings.site_name}><section className="page-head">
        <span className="eyebrow">Portfolio</span>
        <h1>Projects</h1><p>A selection of spatial, residential and cultural work.</p>
    </section><section className="section">
            <div className="projects-grid">{projects.map(p => <ProjectCard key={p.id} project={p} />)}</div>
        </section></SiteShell>
}
