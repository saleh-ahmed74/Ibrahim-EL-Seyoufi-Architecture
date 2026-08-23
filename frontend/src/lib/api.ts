export type Image = {
    url: string;
    alt_text?: string | null; sort_order: number
};
export type Project = {
    id: number;
    title: string;
    slug: string;
    description: string;
    location?: string | null; year?: number | null;
    category?: { name: string; slug: string } | null;
    cover_image?: Image | null; images?: Image[]
};
export type Settings = {
    site_name: string; architect_name: string;
    professional_title: string;
    short_bio?: string;
    bio?: string;
    education?: string;
    experience?: string;
    design_philosophy?: string;
    specializations?: string;
    phone?: string;
    email?: string;
    whatsapp_number?: string;
    whatsapp_message?: string;
    address?: string;
    profile_image?: string;
    hero_image?: string;
    instagram_url?: string;
    facebook_url?: string;
    linkedin_url?: string;
    behance_url?: string;
    pinterest_url?: string;
    meta_title?: string;
    meta_description?: string
};
const api = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
async function get<T>(path: string): Promise<T> { const res = await fetch(`${api}${path}`, { next: { revalidate: 60 } }); if (!res.ok) throw new Error('API request failed'); const json = await res.json(); return json.data; }
export const getSettings = () => get<Settings>('/site-settings');
export const getProjects = (category?: string) =>
    get<Project[]>(`/projects${category ? `?category=${encodeURIComponent(category)}` : ''}`);
export const getProject = (slug: string) => get<Project>(`/projects/${slug}`);
export const getCategories = () => get<{ name: string; slug: string }[]>('/categories');
export const asset = (path?: string | null) => {
    if (!path) return undefined;
    if (path.startsWith('http')) return path;
    // already has /storage prefix
    if (path.startsWith('/storage/') || path.startsWith('storage/')) {
        return `${api.replace('/api', '')}/${path.replace(/^\//, '')}`;
    }
    // uploaded path may include 'public/' prefix
    if (path.startsWith('public/')) {
        return `${api.replace('/api', '')}/storage/${path.replace(/^public\//, '')}`;
    }
    // fallback: assume it's a filename stored in storage
    return `${api.replace('/api', '')}/storage/${path}`;
};
