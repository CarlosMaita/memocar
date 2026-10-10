{{-- Botón "Instalar app" (PWA); solo visible si el navegador permite instalar. variant: menu | banner | card --}}
@props(['variant' => 'menu'])
<div x-data="pwaInstall" x-show="visible" x-cloak
     @if ($variant === 'banner') x-init="try { if (localStorage.getItem('pwa-banner-closed')) installed = true } catch (e) {}" @endif
     {{ $attributes }}>
    @if ($variant === 'banner')
        <div class="card p-4 flex items-center gap-3">
            <img src="/icons/icon-192.png" alt="" class="w-11 h-11 rounded-[10px] shrink-0">
            <div class="min-w-0 flex-1">
                <div class="font-semibold text-white leading-tight">Instala MemoCar</div>
                <div class="text-[13px] text-muted">Ábrela como app desde tu pantalla de inicio.</div>
            </div>
            <button type="button" @click="install()" class="btn-primary min-h-10 px-3.5 text-sm">Instalar</button>
            <button type="button" aria-label="Cerrar" @click="installed = true; try { localStorage.setItem('pwa-banner-closed', '1') } catch (e) {}" class="w-9 h-9 -me-1 flex items-center justify-center text-muted hover:text-white">
                <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @elseif ($variant === 'card')
        <button type="button" @click="install()" class="w-full flex items-center justify-center gap-2.5 min-h-12 rounded-lg border border-line-2 bg-surface text-fg font-semibold hover:border-accent">
            <svg aria-hidden="true" class="w-5 h-5 text-accent-fg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
            Instalar la app en tu teléfono
        </button>
    @else
        <button type="button" @click="install()" class="w-full text-start px-3 py-2.5 md:px-4 md:py-2 rounded-md md:rounded-none flex items-center gap-2 text-base md:text-sm font-medium text-accent-fg hover:bg-surface-2">
            <svg aria-hidden="true" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
            Instalar app
        </button>
    @endif
</div>
