@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Edit Blog Post</h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.blog.update', $post->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $post->title) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt"
                              class="form-control"
                              rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content"
                              class="form-control"
                              rows="6"
                              required>{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cover Image</label>
                    <input type="file"
                           name="cover_image"
                           class="form-control">

                    @if($post->cover_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$post->cover_image) }}"
                                 width="120"
                                 class="rounded shadow-sm">
                        </div>
                    @endif
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox"
                           name="is_published"
                           class="form-check-input"
                           id="publishCheck"
                           {{ $post->is_published ? 'checked' : '' }}>
                    <label class="form-check-label" for="publishCheck">
                        Published
                    </label>
                </div>

                <button type="submit" class="btn btn-dark">
                    Update Post
                </button>

                <a href="{{ route('admin.blog.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
