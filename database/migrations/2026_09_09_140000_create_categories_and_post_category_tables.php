<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'post_id']);
        });

        $posts = DB::table('posts')->select(['id', 'category'])->get();

        foreach ($posts as $post) {
            $categoryNames = collect(explode(',', (string) $post->category))
                ->map(fn (string $category): string => trim($category))
                ->filter()
                ->unique(fn (string $category): string => Str::lower($category));

            foreach ($categoryNames as $categoryName) {
                $slug = Str::slug($categoryName);

                if ($slug === '') {
                    $slug = 'category-'.substr(sha1(Str::lower($categoryName)), 0, 16);
                }

                DB::table('categories')->insertOrIgnore([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $categoryId = DB::table('categories')->where('slug', $slug)->value('id');

                DB::table('category_post')->insertOrIgnore([
                    'category_id' => $categoryId,
                    'post_id' => $post->id,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('categories');
    }
};
