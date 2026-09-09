<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function edit(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->is($post->user) && in_array($post->status, ['draft', 'rejected'], true), 403);

        return redirect()->route('dashboard', ['edit' => $post->id]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'categories' => ['required', 'string', 'max:1000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:100000'],
            'status' => ['required', 'in:draft,pending'],
        ]);

        $status = $validated['status'];
        unset($validated['status']);

        $categoryNames = collect(explode(',', $validated['categories']))
            ->map(fn (string $category): string => trim($category))
            ->filter()
            ->unique(fn (string $category): string => Str::lower($category))
            ->values();

        if ($categoryNames->isEmpty()) {
            return back()
                ->withErrors(['categories' => 'Add at least one category.'])
                ->withInput();
        }

        $validated['category'] = $categoryNames->implode(', ');
        unset($validated['categories']);

        $validated['body'] = $this->sanitizeBody($validated['body']);

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('post-thumbnails', 'public');
            $validated['thumbnail'] = Storage::disk('public')->url($thumbnailPath);
        }

        $post = $request->user()->posts()->create([
            ...$validated,
            'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(6)),
            'status' => $status,
        ]);

        $this->syncCategories($post, $categoryNames);

        return redirect()->route('dashboard')->with('status', $status === 'pending'
            ? 'Your post was sent for review.'
            : 'Your draft was saved.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->is($post->user) && in_array($post->status, ['draft', 'rejected'], true), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'categories' => ['required', 'string', 'max:1000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:100000'],
            'status' => ['required', 'in:draft,pending'],
        ]);

        $categoryNames = collect(explode(',', $validated['categories']))
            ->map(fn (string $category): string => trim($category))
            ->filter()
            ->unique(fn (string $category): string => Str::lower($category))
            ->values();

        if ($categoryNames->isEmpty()) {
            return back()->withErrors(['categories' => 'Add at least one category.'])->withInput();
        }

        $oldThumbnail = $post->thumbnail;
        $status = $validated['status'];
        unset($validated['categories'], $validated['status']);
        $validated['category'] = $categoryNames->implode(', ');
        $validated['body'] = $this->sanitizeBody($validated['body']);

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('post-thumbnails', 'public');
            $validated['thumbnail'] = Storage::disk('public')->url($thumbnailPath);
        }

        $post->update([...$validated, 'status' => $status, 'published_at' => null]);
        $this->syncCategories($post, $categoryNames);

        if ($request->hasFile('thumbnail')) {
            $this->deleteThumbnail($oldThumbnail);
        }

        return redirect()->route('dashboard')->with('status', $status === 'pending'
            ? 'Your revised post was sent for review.'
            : 'Your revised draft was saved.');
    }

    private function syncCategories(Post $post, $categoryNames): void
    {
        $post->categories()->sync($categoryNames->map(function (string $categoryName): int {
            $slug = Str::slug($categoryName);

            if ($slug === '') {
                $slug = 'category-'.substr(sha1(Str::lower($categoryName)), 0, 16);
            }

            return Category::firstOrCreate(['slug' => $slug], ['name' => $categoryName])->id;
        }));
    }

    private function deleteThumbnail(?string $thumbnail): void
    {
        if (! $thumbnail) {
            return;
        }

        $path = ltrim(parse_url($thumbnail, PHP_URL_PATH) ?: $thumbnail, '/');
        $prefix = trim(parse_url(Storage::disk('public')->url(''), PHP_URL_PATH) ?: '', '/');

        if ($prefix && Str::startsWith($path, $prefix.'/')) {
            $path = Str::after($path, $prefix.'/');
        }

        Storage::disk('public')->delete($path);
    }

    private function sanitizeBody(string $body): string
    {
        $allowedTags = '<p><br><strong><em><u><s><ol><ul><li><h1><h2><h3><blockquote><a><code><pre><hr>';
        $body = strip_tags($body, $allowedTags);

        $body = preg_replace_callback('/<a\b[^>]*>/i', function (array $match): string {
            preg_match('/href\s*=\s*(["\'])(.*?)\1/i', $match[0], $hrefMatch);
            $href = $hrefMatch[2] ?? '';

            if (! preg_match('/^(https?:\/\/|\/|#)/i', $href)) {
                return '<a>';
            }

            return '<a href="'.e($href).'" target="_blank" rel="noopener noreferrer">';
        }, $body);

        return preg_replace('/<(p|br|strong|em|u|s|ol|ul|li|h1|h2|h3|blockquote|code|pre|hr)(?:\s[^>]*)?>/i', '<$1>', $body) ?? $body;
    }

    public function index(Request $request): View|JsonResponse
    {
        $posts = Post::approved()->with(['user', 'categories'])->latest('published_at')->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.post-rows', ['posts' => $posts->getCollection()])->render(),
                'nextPageUrl' => $posts->nextPageUrl(),
            ]);
        }

        return view('home', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === 'approved' && $post->published_at?->isPast(), 404);

        // We willen de comments van deze post laten zien, met daarbij de naam van wie de comment plaatste.
        // $post->load(...) haalt die data alvast op, zodat de view geen aparte query per comment hoeft te doen.
        $post->load(['categories', 'comments' => function ($commentsQuery) {
            // Nieuwste comment bovenaan
            $commentsQuery->latest();

            // Ook meteen de 'user' relatie van elke comment ophalen (nodig voor $comment->user->name in de view)
            $commentsQuery->with('user');
        }]);

        return view('post', [
            'post' => $post,
        ]);
    }
}
