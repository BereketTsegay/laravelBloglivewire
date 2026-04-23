<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Posts related to technology, gadgets, and software.',
                'color' => '#6366f1', // indigo-500
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'description' => 'Posts about health, wellness, and fitness.',
                'color' => '#10b981', // emerald-500
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Posts about travel destinations, tips, and experiences.',
                'color' => '#f59e0b', // amber-500
            ],
            [
                'name' => 'Food',
                'slug' => 'food',
                'description' => 'Posts about recipes, restaurants, and food culture.',
                'color' => '#ef4444', // red-500
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        //crate tags
        $tags = [
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'Livewire', 'slug' => 'livewire'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'Vue.js', 'slug' => 'vue-js'],
            ['name' => 'React', 'slug' => 'react'],
            ['name' => 'CSS', 'slug' => 'css'],
            ['name' => 'HTML', 'slug' => 'html'],
        ];

        foreach ($tags as $tag) {
            \App\Models\Tag::create($tag);
        }

    }
}
