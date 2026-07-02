@props(['value'])

<span {{ $attributes->merge(['class' => 'badge']) }}>
    {{ ucwords(str_replace('_', ' ', (string) $value)) }}
</span>
