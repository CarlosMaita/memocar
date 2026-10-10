{{-- Instrucciones de instalación (iPhone/iPad o navegadores sin aviso automático). Se abre con el evento pwa:help. --}}
<div x-data="{ open: false, ios: /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) }"
     @pwa:help.window="open = true" @keydown.escape.window="open = false"
     x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/70 p-4" @click.self="open = false">
    <div role="dialog" aria-modal="true" aria-labelledby="pwa-help-title" class="card w-full max-w-sm p-5 flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <img src="/icons/icon-192.png" alt="" class="w-12 h-12 rounded-xl">
            <h2 id="pwa-help-title" class="m-0 font-display font-extrabold text-xl text-white">Instalar MemoCar</h2>
        </div>
        <ol x-show="ios" class="m-0 ps-5 flex flex-col gap-2 text-fg-2">
            <li>Abre esta página en <span class="text-white font-semibold">Safari</span>.</li>
            <li>Toca el botón <span class="text-white font-semibold">Compartir</span>
                <svg aria-label="Compartir" role="img" class="inline w-4 h-4 -mt-1 text-accent-fg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3M8 7l4-4 4 4M5 12v8h14v-8"/></svg>
                en la barra inferior.</li>
            <li>Elige <span class="text-white font-semibold">Agregar a pantalla de inicio</span> y confirma.</li>
        </ol>
        <ol x-show="! ios" class="m-0 ps-5 flex flex-col gap-2 text-fg-2">
            <li>Abre el menú del navegador (los tres puntos).</li>
            <li>Elige <span class="text-white font-semibold">Instalar app</span> o <span class="text-white font-semibold">Agregar a pantalla principal</span>.</li>
        </ol>
        <button type="button" @click="open = false" class="btn-primary w-full">Entendido</button>
    </div>
</div>
