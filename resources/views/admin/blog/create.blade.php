@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Create Blog Post</h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.blog.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt"
                              class="form-control"
                              rows="2">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content"
                              class="form-control"
                              rows="6"
                              required>{{ old('content') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cover Image</label>
                    <input type="file"
                           name="cover_image"
                           class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox"
                           name="is_published"
                           class="form-check-input"
                           id="publishCheck">
                    <label class="form-check-label" for="publishCheck">
                        Publish Immediately
                    </label>
                </div>

                <button type="submit" class="btn btn-dark">
                    Save Post
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
