@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Blog Posts</h3>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-dark">
            <i class="fa fa-plus"></i> Add New Post
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published At</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $post->title }}</td>
                                <td>
                                    @if($post->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.blog.edit', $post->id) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.blog.destroy', $post->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this post?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No blog posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $posts->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
