@extends('user.layouts.master')

@section('user-content')
<div class="bg-light py-5 mt-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">LasaWheels Blog</h2>
                <p class="text-muted">Latest travel and rental updates.</p>
            </div>

            <form method="GET" action="{{ route('blog.index') }}">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search..."
                       class="form-control">
            </form>
        </div>

        @if($posts->count() == 0)
            <div class="text-center py-5">
                <h5>No blog posts available.</h5>
            </div>
        @else

        <div class="row g-4">
            @foreach($posts as $post)
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm">

                        @if($post->cover_image)
                            <img src="{{ asset('storage/'.$post->cover_image) }}"
                                 class="card-img-top"
                                 style="height:220px; object-fit:cover;">
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="{{ route('blog.show', $post->slug) }}"
                                   class="text-decoration-none text-dark">
                                    {{ $post->title }}
                                </a>
                            </h5>

                            <p class="text-muted small">
                                {{ $post->published_at?->format('d M Y') }}
                            </p>

                            <p class="card-text">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>

                            <a href="{{ route('blog.show', $post->slug) }}"
                               class="btn btn-outline-dark mt-auto">
                                Read More
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $posts->withQueryString()->links() }}
        </div>

        @endif

    </div>
</div>
@endsection
