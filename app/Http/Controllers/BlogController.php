<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts      = BlogPost::published()->latest()->with('category', 'author')->paginate(9);
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug): View
    {
        $post   = BlogPost::where('slug', $slug)->published()->firstOrFail();
        $recent = BlogPost::published()->latest()->where('id', '!=', $post->id)->take(4)->get();

        return view('blog.show', compact('post', 'recent'));
    }
}
