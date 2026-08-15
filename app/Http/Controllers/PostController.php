<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('public.index');
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['user', 'categories', 'comments' => fn ($query) => $query->approved()->oldest()])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.show', ['post' => $post]);
    }
}
