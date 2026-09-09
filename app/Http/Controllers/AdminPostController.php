<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'pendingPosts' => Post::where('status', 'pending')->with(['user', 'categories'])->latest()->get(),
            'allPosts' => Post::with(['user', 'categories'])->latest()->get(),
            'pendingCount' => Post::where('status', 'pending')->count(),
            'approvedCount' => Post::where('status', 'approved')->count(),
            'rejectedCount' => Post::where('status', 'rejected')->count(),
        ]);
    }

    public function approve(Post $post): RedirectResponse
    {
        $post->update([
            'status' => 'approved',
            'published_at' => now(),
        ]);

        return back()->with('status', 'The post was approved and is now live.');
    }

    public function reject(Post $post): RedirectResponse
    {
        $this->takeOffline($post);

        return back()->with('status', 'The post was rejected.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $thumbnail = $post->thumbnail;
        $post->delete();
        $this->deleteThumbnail($thumbnail);

        return back()->with('status', 'The post was permanently deleted.');
    }

    private function deleteThumbnail(?string $thumbnail): void
    {
        if (! $thumbnail) {
            return;
        }

        $thumbnailPath = parse_url($thumbnail, PHP_URL_PATH) ?: $thumbnail;
        $diskUrlPath = parse_url(Storage::disk('public')->url(''), PHP_URL_PATH);
        $diskUrlPrefix = trim($diskUrlPath ?: '', '/');
        $thumbnailPath = ltrim($thumbnailPath, '/');

        if ($diskUrlPrefix && Str::startsWith($thumbnailPath, $diskUrlPrefix.'/')) {
            $thumbnailPath = Str::after($thumbnailPath, $diskUrlPrefix.'/');
        }

        Storage::disk('public')->delete($thumbnailPath);
    }

    private function takeOffline(Post $post): void
    {
        $post->update([
            'status' => 'rejected',
            'published_at' => null,
        ]);
    }
}
