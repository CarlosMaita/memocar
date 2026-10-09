<x-app-layout>
    <x-slot name="title">Mis carros</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow text-accent-fg">Garaje</span>
                <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">Mis carros</h1>
            </div>
            <a href="{{ route('cars.create') }}" class="btn-primary">
                <svg aria-hidden="true" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Agregar carro
            </a>
        </div>
    </x-slot>

    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-muted text-left border-b border-line">
                <tr>
                    <th class="px-5 py-3 font-semibold">Carro</th>
                    <th class="px-5 py-3 font-semibold">Placa</th>
                    <th class="px-5 py-3 font-semibold text-right">Kilometraje</th>
                    <th class="px-5 py-3 font-semibold text-right">Registros</th>
                    <th class="px-5 py-3 font-semibold text-right">Gastado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cars as $car)
                    <tr class="border-b border-line-soft last:border-b-0">
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-white">{{ $car->name }}</span>
                            @if ($car->id === $selectedId)
                                <span class="ms-1.5 pill bg-ok-bg text-ok">seleccionado</span>
                            @endif
                            <div class="text-muted">{{ $car->description() }}</div>
                        </td>
                        <td class="px-5 py-3.5">{{ $car->plate ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-mono">{{ km($car->current_km) }}</td>
                        <td class="px-5 py-3.5 text-right">{{ $car->records_count }}</td>
                        <td class="px-5 py-3.5 text-right font-mono">{{ money($car->records_sum_cost) }}</td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <form method="POST" action="{{ route('cars.choose', $car) }}" class="inline">
                                @csrf
                                <button class="text-accent-fg font-semibold hover:underline">Ver</button>
                            </form>
                            <a href="{{ route('cars.edit', $car) }}" class="ms-4 text-muted hover:text-white">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-muted">Aún no tienes carros registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
