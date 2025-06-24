@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active d-md-none'
            : 'nav-link d-md-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
