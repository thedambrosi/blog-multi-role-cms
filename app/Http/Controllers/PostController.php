<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->with('user')
            ->latest('published_at')
            ->paginate(12);

        return view('public.index', ['posts' => $posts]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with('user')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.show', ['post' => $post]);
    }
}
