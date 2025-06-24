@props(['disabled' => false])

@php
$classes = 'form-control';
if ($attributes->get('class')) {
    $classes .= ' ' . $attributes->get('class');
}
if ($errors->has($attributes->get('name'))) {
    $classes .= ' is-invalid';
}
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
