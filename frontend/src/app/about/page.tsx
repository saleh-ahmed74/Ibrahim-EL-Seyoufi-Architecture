import SiteShell from '@/components/site-shell';
import { getSettings, asset } from '@/lib/api';
export const dynamic = 'force-dynamic';
export default async function About() {
    const s = await getSettings();
    return <SiteShell name={s.site_name}><section className="about-page"><div>
        <span className="eyebrow">About</span>
        <h1>{s.architect_name}</h1>
        <p className="title">{s.professional_title}</p>
        {s.profile_image && <img className="profile" src={asset(s.profile_image)}
            alt={s.architect_name} />}</div><div className="prose"><p>{s.bio}</p>
            <h2>Education</h2><p>{s.education}</p><h2>Experience</h2>
            <p>{s.experience}</p><h2>Design philosophy</h2>
            <p>{s.design_philosophy}</p><h2>Specializations</h2>
            <p>{s.specializations}</p></div></section></SiteShell>
}
