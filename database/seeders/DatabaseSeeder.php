<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Residential', 'Commercial', 'Interior Design', 'Landscape', 'Cultural'] as $sortOrder => $name) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $sortOrder, 'is_active' => true],
            );
        }

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Ibrahim EL-Seyoufi Architecture',
                'architect_name' => 'Ibrahim EL-Seyoufi',
                'professional_title' => 'Architect & Spatial Designer',
                'short_bio' => 'Designing contemporary spaces where architecture, light, material, and human experience come together.',
                'bio' => 'Ibrahim EL-Seyoufi is an architect focused on contemporary residential, commercial, and interior architecture.',
                'education' => 'Bachelor of Architecture',
                'experience' => '8+ years of experience in architectural design and visualization.',
                'design_philosophy' => 'Architecture should respond to its context while creating spaces that are simple, functional, and meaningful.',
                'specializations' => 'Residential Architecture · Interior Design · Commercial Architecture',
                'phone' => '+201146677219',
                'email' => 'hello@example.com',
                'whatsapp_number' => '+201146677219',
                'whatsapp_message' => 'Hello, I am interested in your architectural services.',
                'address' => 'Cairo, Egypt',
                'profile_image' => '01M0FBFZD6SGTPSEKNR2555EFX.jpg',
                'hero_image' => '01M0FCNS6AZ0YATS76GKZ98WSS.jpg',
            ],
        );

        $this->call(ProjectsSeeder::class);

        if (App::environment(['local', 'testing']) && env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            \App\Models\User::query()->updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => env('ADMIN_NAME', 'Site Administrator'),
                    'password' => Hash::make(env('ADMIN_PASSWORD')),
                    'is_admin' => true,
                ],
            );
        }
    }
}
