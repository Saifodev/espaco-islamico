<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Content\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['Laravel', '#FF2D20'],
            ['VueJS', '#42B883'],
            ['API', '#6366F1'],
            ['Backend', '#0EA5E9'],
            ['Frontend', '#EC4899'],
            ['DevOps', '#8B5CF6'],
            ['Startup', '#F97316'],
            ['Productividade', '#14B8A6'],
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag[0],
                'slug' => Str::slug($tag[0]),
                'color' => $tag[1],
            ]);
        }
    }
}