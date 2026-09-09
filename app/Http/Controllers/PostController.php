<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $posts = Post::whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at')->paginate(6);

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
        return view('post', [
            'post' => $post,
        ]);
    }
}
