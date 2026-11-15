<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! Category::query()->exists()) {
            foreach (['Residential', 'Commercial', 'Interior Design', 'Landscape', 'Cultural'] as $sortOrder => $name) {
                Category::query()->firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'sort_order' => $sortOrder, 'is_active' => true],
                );
            }
        }

        SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'Ibrahim EL-Seyoufi Architecture',
            'architect_name' => 'Ibrahim EL-Seyoufi',
            'professional_title' => 'Architect & Spatial Designer',
        ]);

        if (! Project::withTrashed()->exists()) {
            $this->call(ProjectsSeeder::class);
        }

        if (App::environment(['local', 'testing']) && env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::query()->firstOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => env('ADMIN_NAME', 'Site Administrator'),
                    'password' => env('ADMIN_PASSWORD'),
                    'is_admin' => true,
                ],
            );
        }
    }
}
