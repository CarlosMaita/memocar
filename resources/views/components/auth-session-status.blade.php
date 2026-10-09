@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-ok-line bg-ok-bg px-3 py-2 text-sm text-ok-soft']) }}>
        {{ $status }}
    </div>
@endif
