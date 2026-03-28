<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index()
    {
        $posts = BlogPost::latest()->paginate(10);

        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Display the specified blog post.
     */
    public function show(BlogPost $blog)
    {
        $post = $blog->load('author');

        return view('admin.blog.show', compact('post'));
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        $slug = Str::slug($request->title);

        // Prevent duplicate slug
        if (BlogPost::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $imagePath = null;

        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')
                ->store('blog', 'public');
        }

        BlogPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'cover_image' => $imagePath,
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog created successfully');
    }

    /**
     * Show the form for editing the blog post.
     */
    public function edit(BlogPost $blog)
    {
        $post = $blog;
        return view('admin.blog.edit', compact('post'));
    }
    /**
     * Update the specified blog post.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        $slug = Str::slug($request->title);

        if (BlogPost::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
            $slug = $slug . '-' . time();
        }

        if ($request->hasFile('cover_image')) {
            $blog->cover_image = $request->file('cover_image')
                ->store('blog', 'public');
        }

        $blog->update([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog updated successfully');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(BlogPost $blog)
    {
        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog deleted successfully');
    }
}
