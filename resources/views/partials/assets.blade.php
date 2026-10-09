{{-- Sin build step: el servidor no tiene Node. Tailwind v4 (browser) y Alpine desde CDN con versión fija. --}}
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.1.11"></script>
<style type="text/tailwindcss">
    @theme {
        --font-sans: Figtree, ui-sans-serif, system-ui, sans-serif;
    }
    @layer base {
        *, ::after, ::before, ::backdrop { border-color: var(--color-gray-200, currentColor); }
        button:not(:disabled), [role="button"]:not(:disabled) { cursor: pointer; }
        input:not([type=checkbox]):not([type=radio]), select, textarea { border-width: 1px; padding: 0.5rem 0.75rem; }
        [x-cloak] { display: none !important; }
    }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
