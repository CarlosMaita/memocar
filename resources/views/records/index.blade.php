<x-app-layout>
    <x-slot name="title">Registros</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow text-accent-fg">{{ $currentCar->name }} · historial</span>
                <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">Registros</h1>
            </div>
            <a href="{{ route('records.create') }}" class="btn-primary min-h-[46px]">
                <svg aria-hidden="true" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Nuevo registro
            </a>
        </div>
    </x-slot>

    <div class="flex flex-col gap-5">
        {{-- Filtros --}}
        <form method="GET" aria-label="Filtros" class="card px-4 py-3.5 flex flex-wrap items-end gap-3">
            @if ($filters['year'] ?? null)
                <input type="hidden" name="year" value="{{ $filters['year'] }}">
            @endif
            <label class="flex flex-col gap-1 text-[13px] font-semibold text-muted">
                Concepto
                <select name="type" class="min-h-[42px] min-w-[240px] text-[15px] font-normal" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" @selected(($filters['type'] ?? null) == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </label>
            @if ($years->isNotEmpty())
                <div class="flex flex-col gap-1">
                    <span class="text-[13px] font-semibold text-muted">Año</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ([null, ...$years] as $year)
                            @php($on = ($filters['year'] ?? null) == $year)
                            <a href="{{ route('records.index', array_filter(['type' => $filters['type'] ?? null, 'year' => $year])) }}" @if ($on) aria-current="true" @endif
                               @class([
                                   'min-h-[42px] px-3.5 rounded-lg border flex items-center text-sm no-underline',
                                   'border-accent bg-accent text-on-accent! font-bold' => $on,
                                   'border-line-2 bg-bg text-fg! font-semibold hover:border-muted' => ! $on,
                               ])>{{ $year ?? 'Todos' }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="ms-auto text-right">
                <div class="text-[13px] text-muted">Total · {{ $count }} {{ $count === 1 ? 'registro' : 'registros' }}</div>
                <div class="font-mono font-semibold text-2xl text-accent-fg">{{ money($total) }}</div>
            </div>
        </form>

        {{-- Línea de tiempo por año --}}
        @forelse ($recordsByYear as $year => $yearRecords)
            <section class="flex flex-col gap-2.5">
                <div class="flex items-baseline justify-between gap-3 px-1 pt-2">
                    <h2 class="m-0 font-display font-extrabold text-[22px] text-white">{{ $year }}</h2>
                    <span class="font-mono text-sm text-muted">{{ money($yearTotals[$year] ?? 0) }}</span>
                </div>
                <div class="card overflow-hidden">
                    @foreach ($yearRecords as $record)
                        <a href="{{ route('records.edit', $record) }}" class="grid grid-cols-[56px_minmax(0,1fr)_auto] sm:grid-cols-[64px_minmax(0,1fr)_auto_auto] gap-x-4 sm:gap-x-[18px] gap-y-1 items-center px-4 sm:px-5 py-3.5 border-b border-line-soft last:border-b-0 text-fg no-underline hover:bg-surface-2 hover:text-fg">
                            <span class="row-span-2 sm:row-span-1 flex flex-col items-center py-1.5 rounded-lg bg-surface-2 leading-tight">
                                <span class="font-bold text-lg text-white">{{ $record->date->format('d') }}</span>
                                <span class="text-[11px] uppercase text-muted">{{ $record->date->translatedFormat('M') }}</span>
                            </span>
                            <span class="min-w-0 flex flex-col gap-0.5">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-white">{{ $record->type->name }}</span>
                                    <span class="px-2 py-px rounded bg-surface-2 text-xs font-semibold text-muted">{{ $record->type->category }}</span>
                                </span>
                                <span class="text-sm text-muted">{{ $record->description ?: 'Sin descripción' }}</span>
                            </span>
                            <span class="hidden sm:block font-mono text-sm text-muted whitespace-nowrap">{{ km($record->km) }}</span>
                            <span class="font-mono font-semibold whitespace-nowrap text-right sm:min-w-[84px]">{{ money($record->cost) }}<span class="sm:hidden block font-normal text-xs text-muted">{{ km($record->km) }}</span></span>
                        </a>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="card px-6 py-12 text-center text-muted">
                No hay registros {{ array_filter($filters) ? 'con estos filtros' : 'para este carro' }}.
                @unless (array_filter($filters))
                    <div class="mt-4"><a href="{{ route('records.create') }}" class="btn-primary">Registrar el primero</a></div>
                @endunless
            </div>
        @endforelse

        {{ $records->links() }}
    </div>
</x-app-layout>
