import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
	images: { remotePatterns: [{ protocol: 'http', hostname: 'localhost' }, { protocol: 'https', hostname: '**' }] },
	// Allow the dev server to load assets from 127.0.0.1 when accessing via that host
	allowedDevOrigins: ['http://127.0.0.1', 'http://localhost'],
};

export default nextConfig;
