@extends('admin.layouts.master')

@section('title', 'Blog Posts')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Blog Posts</h2>
        <p class="text-muted">Manage your blog content</p>
    </div>
    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
        + Add New Post
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        @if($posts->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                            <tr class="cursor-pointer"
                                role="button"
                                tabindex="0"
                                onclick="window.location='{{ route('admin.blog.show', $post) }}'"
                                onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('admin.blog.show', $post) }}'; }">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold">
                                    <a href="{{ route('admin.blog.show', $post) }}" class="text-decoration-none text-dark">
                                        {{ $post->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($post->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No blog posts found</h5>
                <p class="text-muted">Create your first blog post.</p>
            </div>
        @endif
    </div>
</div>
@endsection
