<section class="bg-light">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="sec-heading center">
                    <h2>{!! clean($title) !!}</h2>
                    <p>{!! clean($description) !!}</p>
                </div>
            </div>
        </div>

        <div class="row list-layout">
            @foreach($projects as $project)
            <div class="col-lg-4 col-md-6 col-sm-12">
                {!! Theme::partial('real-estate.projects.item', compact('project')) !!}
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                <a href="{{ route('public.projects') }}" class="btn btn-theme-light-2 rounded">{{ __('Browse More Projects') }}</a>
            </div>
        </div>
    </div>
</section>
