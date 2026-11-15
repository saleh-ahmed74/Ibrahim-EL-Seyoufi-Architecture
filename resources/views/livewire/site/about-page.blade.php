<section class="about-page">
    <div class="about-intro">
        <span class="eyebrow">About</span>
        <h1>{{ $settings->architect_name }}</h1>
        <p class="title">{{ $settings->professional_title }}</p>
        @if ($settings->profile_image)
            <img class="profile" src="{{ $settings->publicImageUrl($settings->profile_image) }}" alt="{{ $settings->architect_name }}" loading="lazy">
        @endif
    </div>
    <div class="prose">
        @if ($settings->bio)<p>{{ $settings->bio }}</p>@endif
        @foreach (['Education' => $settings->education, 'Experience' => $settings->experience, 'Design philosophy' => $settings->design_philosophy, 'Specializations' => $settings->specializations] as $heading => $content)
            @if ($content)<h2>{{ $heading }}</h2><p>{{ $content }}</p>@endif
        @endforeach
    </div>
</section>
