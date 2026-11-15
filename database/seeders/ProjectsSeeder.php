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
            ],
            [
                'title' => 'City Office',
                'slug' => 'city-office',
                'description' => 'Modern office fit-out in the city center.',
                'location' => 'Cairo, Egypt',
                'year' => 2023,
            ],
            [
                'title' => 'Minimal Apartment',
                'slug' => 'minimal-apartment',
                'description' => 'Compact apartment emphasizing light and materiality.',
                'location' => 'Cairo, Egypt',
                'year' => 2022,
            ],
        ];

        foreach ($projects as $i => $p) {
            Project::firstOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'],
                'description' => $p['description'],
                'location' => $p['location'],
                'year' => $p['year'],
                'sort_order' => $i
            ]);
        }
    }
}
