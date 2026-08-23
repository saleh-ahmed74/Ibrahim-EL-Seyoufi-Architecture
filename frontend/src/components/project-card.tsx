import Link from 'next/link';
import { Project, asset } from '@/lib/api';
export default function ProjectCard({ project }:
    { project: Project }) {
        const src = asset(project.cover_image?.url);
    return <Link className="project-card" href={`/projects/${project.slug}`}>
        <div className="project-image">{src ?
            <img src={src} alt={project.cover_image?.alt_text || project.title} /> :
            <div className="image-placeholder" />}</div>
        <div className="project-meta"><h3>{project.title}</h3>
            <p>{project.category?.name || 'Architecture'} <i />
                {project.location || 'Cairo'} <i /> {project.year}</p></div></Link>
}
