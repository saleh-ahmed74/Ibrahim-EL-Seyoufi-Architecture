<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Coastal Villa',
                'slug' => 'coastal-villa',
                'description' => 'A contemporary coastal villa with large glazed facades.',
                'location' => 'Alexandria, Egypt',
                'year' => 2024,
                'cover_url' => 'projects/01M0D89MDNN6KZ46VJ7V0WQDTJ.png'
            ],
            [
                'title' => 'City Office',
                'slug' => 'city-office',
                'description' => 'Modern office fit-out in the city center.',
                'location' => 'Cairo, Egypt',
                'year' => 2023,
                'cover_url' => 'projects/01M0D89MDYZRQ1KW1844DGDA50.jpg'
            ],
            [
                'title' => 'Minimal Apartment',
                'slug' => 'minimal-apartment',
                'description' => 'Compact apartment emphasizing light and materiality.',
                'location' => 'Cairo, Egypt',
                'year' => 2022,
                'cover_url' => 'projects/01M0D89ME2RVWF0A5CQYXDHCPE.png'
            ],
        ];

        foreach ($projects as $i => $p) {
            $proj = Project::updateOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'],
                'description' => $p['description'],
                'location' => $p['location'],
                'year' => $p['year'],
                'sort_order' => $i
            ]);

            // Use the public placeholder URL as a cover image record if ProjectImage model exists
            try {
                if (class_exists(\App\Models\ProjectImage::class)) {
                    \App\Models\ProjectImage::updateOrCreate([
                        'project_id' => $proj->id,
                        'is_cover' => true
                    ], [
                        'image_path' => $p['cover_url'],
                        'alt_text' => $p['title'],
                        'sort_order' => 0,
                        'is_cover' => true
                    ]);
                }
            } catch (\Throwable $e) {
                // ignore if model not available
            }
        }
    }
}
