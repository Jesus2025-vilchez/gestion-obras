@props(['messages'])

@if ($messages)
    @foreach ((array) $messages as $message)
        <div {{ $attributes->merge(['class' => 'invalid-feedback']) }}>
            {{ $message }}
        </div>
    @endforeach
@endif
