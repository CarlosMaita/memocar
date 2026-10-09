@props(['size' => 'md'])
@php($box = $size === 'lg' ? 'w-10 h-10 rounded-[9px]' : 'w-[34px] h-[34px] rounded-lg')
<span {{ $attributes->merge(['class' => 'flex items-center gap-2.5 text-white']) }}>
    <span class="{{ $box }} bg-accent flex items-center justify-center shrink-0">
        <x-application-logo class="{{ $size === 'lg' ? 'w-6 h-6' : 'w-5 h-5' }} text-on-accent" />
    </span>
    <span class="font-display font-extrabold tracking-tight {{ $size === 'lg' ? 'text-2xl' : 'text-xl' }}">MemoCar</span>
</span>
