@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'dropdown-menu-start';
        break;
    case 'right':
    default:
        $alignmentClasses = 'dropdown-menu-end';
        break;
}
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
        class="dropdown-menu {{ $alignmentClasses }}"
        style="display: none;"
        @click="open = false">
        <div class="{{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
