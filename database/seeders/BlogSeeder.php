<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use Faker\Factory as Faker;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $numberOfBlogs = 2;

        for ($i = 1; $i <= $numberOfBlogs; $i++) {
            $this->createBlog([
                'title' => "Blog Post $i",
                'description' => $faker->paragraph,
                'image' => "https://example.com/image$i.jpg",
            ]);
        }
    }

    /**
     * Create a blog entry.
     *
     * @param array $attributes
     * @return void
     */
    private function createBlog(array $attributes)
    {
        Blog::create(array_merge($attributes, [
            'uuid' => (string) \Str::uuid(),
        ]));
    }
}
