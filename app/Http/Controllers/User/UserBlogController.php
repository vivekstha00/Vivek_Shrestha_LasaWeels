<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;

class UserBlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('is_published', true)
            ->whereNotNull('published_at');

        // Optional search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('content', 'like', '%' . $request->q . '%');
            });
        }

        $posts = $query->orderByDesc('published_at')
                       ->paginate(9);

        return view('user.pages.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('user.pages.blog.blog-show', compact('post'));
    }
}
