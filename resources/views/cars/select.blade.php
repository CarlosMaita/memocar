<x-app-layout>
    <x-slot name="title">Elegir carro</x-slot>

    <div class="max-w-[1000px] mx-auto flex flex-col gap-7 pt-4">
        <div>
            <span class="eyebrow text-accent-fg">Hola, {{ Auth::user()->name }}</span>
            <h1 class="mt-1 mb-0 font-display font-extrabold text-3xl sm:text-[42px] tracking-tight leading-tight text-white">¿Qué carro vas a revisar?</h1>
            <p class="mt-2 mb-0 text-muted">El dashboard, los registros y las alertas se muestran para el carro que elijas. Puedes cambiarlo cuando quieras.</p>
        </div>

        <div class="grid gap-4 grid-cols-[repeat(auto-fill,minmax(280px,1fr))]">
            @foreach ($cars as $car)
                @php($isCurrent = $car->id === $selectedId)
                <form method="POST" action="{{ route('cars.choose', $car) }}">
                    @csrf
                    <button @class([
                        'w-full h-full text-start flex flex-col gap-[18px] p-[22px] rounded-[14px] bg-surface text-fg transition',
                        'border border-accent shadow-[0_0_0_3px_rgba(118,185,0,0.15)]' => $isCurrent,
                        'border border-line hover:border-accent' => ! $isCurrent,
                    ])>
                        <span class="flex justify-between items-start gap-3">
                            <span @class(['w-[52px] h-[52px] rounded-xl flex items-center justify-center', 'bg-accent text-on-accent' => $isCurrent, 'bg-surface-2 text-fg' => ! $isCurrent])>
                                <x-application-logo class="w-[30px] h-[30px]" />
                            </span>
                            @if ($overdue[$car->id] > 0)
                                <span class="pill bg-over-bg text-over">{{ $overdue[$car->id] }} {{ $overdue[$car->id] === 1 ? 'vencido' : 'vencidos' }}</span>
                            @elseif ($car->records_count === 0)
                                <span class="pill bg-surface-2 text-muted">Sin registros</span>
                            @else
                                <span class="pill bg-ok-bg text-ok">Al día</span>
                            @endif
                        </span>
                        <span>
                            <span class="block font-display font-extrabold text-2xl leading-tight text-white">{{ $car->name }}</span>
                            <span class="block text-muted">{{ collect([$car->description(), $car->plate])->filter()->join(' · ') ?: 'Sin detalles' }}</span>
                        </span>
                        <span class="flex justify-between pt-3.5 border-t border-line text-sm mt-auto">
                            <span @class(['font-mono font-semibold', 'text-accent-fg' => $isCurrent])>{{ km($car->current_km) }}</span>
                            <span class="text-muted">{{ $car->records_count }} {{ $car->records_count === 1 ? 'registro' : 'registros' }}</span>
                        </span>
                    </button>
                </form>
            @endforeach

            <a href="{{ route('cars.create') }}" class="min-h-[200px] flex flex-col items-center justify-center gap-2.5 p-[22px] rounded-[14px] border border-dashed border-[#3A403A] text-muted no-underline font-semibold hover:border-accent hover:text-white">
                <span class="w-11 h-11 rounded-full bg-surface border border-line-2 flex items-center justify-center">
                    <svg aria-hidden="true" class="w-[22px] h-[22px] text-accent-fg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                Agregar otro carro
            </a>
        </div>
    </div>
</x-app-layout>
