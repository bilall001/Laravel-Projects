<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            // Engineering (web & related)
            'Frontend Engineer',
            'Backend Engineer',
            'Full-Stack Engineer',   
            // Quality
            'QA Engineer',
            'Test Engineer',
            // Product & Experience
            'UI/UX Designer',
        ];

        // Prepare rows (avoid duplicates by slug)
        $rows = collect($roles)->map(function ($name) use ($now) {
            $slug = Str::slug($name);
            return [
                'name' => $name,
                'slug' => $slug,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        // Use upsert to avoid inserting duplicates if you run seeder multiple times
        DB::table('roles')->upsert(
            $rows,
            ['slug'],             // unique by slug
            ['name', 'updated_at'] // update these on conflict
        );
    }
}
