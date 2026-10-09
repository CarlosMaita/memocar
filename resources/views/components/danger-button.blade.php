<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center min-h-10 px-4 rounded-lg border border-over-line bg-over-bg text-over font-semibold text-sm hover:border-over focus:outline-none focus:ring-2 focus:ring-over-bar/40']) }}>
    {{ $slot }}
</button>
