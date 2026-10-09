@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-3 py-2.5 rounded-md text-start text-base font-semibold text-white bg-surface-2 shadow-[inset_3px_0_0_var(--color-accent)] no-underline'
            : 'block w-full px-3 py-2.5 rounded-md text-start text-base font-medium text-muted hover:text-white hover:bg-surface no-underline';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
