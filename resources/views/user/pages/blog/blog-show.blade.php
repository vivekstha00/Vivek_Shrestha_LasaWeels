@extends('user.layouts.master')

@section('user-content')
<div class="bg-light py-4">
    <div class="container">

        <a href="{{ route('blog.index') }}"
           class="btn btn-sm btn-outline-secondary mb-4">
            ← Back to Blog
        </a>

        <div class="card shadow-sm">
            @if($post->cover_image)
                <img src="{{ asset('storage/'.$post->cover_image) }}"
                     class="card-img-top"
                     style="height:350px; object-fit:cover;">
            @endif

            <div class="card-body p-4">
                <h2 class="fw-bold">{{ $post->title }}</h2>

                <p class="text-muted small">
                    {{ $post->published_at?->format('d M Y') }}
                </p>

                <hr>
                <div>
                    {!! $post->excerpt !!}
                </div>
                <br>
                <div>
                    {!! $post->content !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
