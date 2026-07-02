@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $subtitle)
        <div class="card-heading">
            @if ($title)
                <h2>{{ $title }}</h2>
            @endif
            @if ($subtitle)
                <p class="muted">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
