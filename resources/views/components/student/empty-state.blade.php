@props(['message' => 'No records found.'])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    {{ $message }}
</div>
