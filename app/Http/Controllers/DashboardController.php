<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $posts = $request->user()->posts()->latest()->get();
        $editingPost = $posts->firstWhere('id', $request->integer('edit'));

        return view('dashboard', [
            'posts' => $posts,
            'draftCount' => $posts->where('status', 'draft')->count(),
            'pendingCount' => $posts->where('status', 'pending')->count(),
            'publishedCount' => $posts->where('status', 'approved')->count(),
            'categories' => Category::query()->orderBy('name')->pluck('name'),
            'editingPost' => $editingPost,
        ]);
    }
}
