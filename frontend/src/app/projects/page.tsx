import ProjectCard from '@/components/project-card';
import SiteShell from '@/components/site-shell';
import { getProjects, getCategories, getSettings } from '@/lib/api';
import CategorySelect from '@/components/category-select';

export const dynamic = 'force-dynamic';

export default async function ProjectsPage({
    searchParams,
}: {
    searchParams: Promise<{ category?: string }>;
}) {
    const { category } = await searchParams;
    const [projects, categories, settings] = await Promise.all([
        getProjects(category),
        getCategories(),
        getSettings(),
    ]);

    return (
        <SiteShell name={settings.site_name} social={settings}>
            <section className="section">
                <div className="section-head">
                    <span className="eyebrow">Portfolio</span>
                    <h1>Projects</h1>
                    <p>A selection of spatial, residential and cultural work.</p>
                </div>

                <CategorySelect categories={categories} current={category ?? ''} />

                <div className="projects-grid">
                    {projects.map((p) => (
                        <ProjectCard key={p.id} project={p} />
                    ))}
                </div>
            </section>
        </SiteShell>
    );
}