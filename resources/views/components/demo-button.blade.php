{{-- Acceso demo sin cuenta --}}
<div class="flex flex-col gap-3">
    <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-widest text-muted" aria-hidden="true">
        <span class="h-px flex-1 bg-line"></span>o<span class="h-px flex-1 bg-line"></span>
    </div>
    <form method="POST" action="{{ route('demo.start') }}">
        @csrf
        <button class="btn-secondary w-full min-h-12 text-base">
            <svg aria-hidden="true" class="w-5 h-5 text-accent-fg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            Ver como demo
        </button>
    </form>
    <p class="m-0 text-[13px] text-muted text-center">Sin registrarte: entras a una cuenta de prueba con 2 carros y su historial. Se borra en 24 horas.</p>
</div>
