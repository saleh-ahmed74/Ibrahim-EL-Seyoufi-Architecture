<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Ibrahim EL-Seyoufi'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'ChangeMe123!'))
            ]
        );
        foreach (['Residential', 'Commercial', 'Interior Design', 'Landscape', 'Cultural']
            as $i => $name)
            \App\Models\Category::updateOrCreate
            (
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name, 'sort_order' => $i]
            );
        \App\Models\SiteSetting::updateOrCreate
        (['id' => 1], [
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
                'hero_image' => '01M0FCNS6AZ0YATS76GKZ98WSS.jpg'
            ]);

        $this->call(\Database\Seeders\ProjectsSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
