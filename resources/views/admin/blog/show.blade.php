@extends('admin.layouts.master')

@section('title', 'Blog Details')

@section('admin-content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Blog Details</h3>
            <p class="text-muted mb-0">Read and manage this post</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary btn-sm">
                Back
            </a>
            <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-primary btn-sm">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('admin.blog.destroy', $post) }}"
                  onsubmit="return confirm('Delete this post?')"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                <h2 class="fw-bold mb-0">{{ $post->title }}</h2>
                <div class="d-flex align-items-center gap-2">
                    @if($post->is_published)
                        <span class="badge bg-success">Published</span>
                    @else
                        <span class="badge bg-secondary">Draft</span>
                    @endif
                </div>
            </div>

            <div class="text-muted small mb-4">
                <span>Author: {{ $post->author?->name ?? 'Admin' }}</span>
                <span class="mx-2">•</span>
                <span>Created: {{ $post->created_at?->format('d M Y, h:i A') }}</span>
                <span class="mx-2">•</span>
                <span>Published: {{ $post->published_at?->format('d M Y, h:i A') ?? 'Not published yet' }}</span>
            </div>

            @if($post->cover_image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $post->cover_image) }}"
                         alt="{{ $post->title }}"
                         class="w-100 rounded-3"
                         style="max-height: 420px; object-fit: cover;">
                </div>
            @endif

            @if($post->excerpt)
                <div class="border-start border-4 border-primary ps-3 mb-4">
                    <p class="mb-0 text-muted">{{ $post->excerpt }}</p>
                </div>
            @endif

            <div class="blog-content" style="line-height: 1.8; font-size: 1rem; white-space: pre-line;">
                {{ $post->content }}
            </div>
        </div>
    </div>
</div>
@endsection
