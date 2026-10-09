@props(['active'])

<a {{ $attributes->merge(['class' => ($active ?? false) ? 'px-3.5 py-2 rounded-md text-sm font-semibold text-white bg-surface-2 no-underline' : 'px-3.5 py-2 rounded-md text-sm font-medium text-muted hover:text-white no-underline']) }}>
    {{ $slot }}
</a>
