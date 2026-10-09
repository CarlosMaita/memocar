@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-fg-2']) }}>
    {{ $value ?? $slot }}
</label>
