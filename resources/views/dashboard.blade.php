<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php($counts = $items->countBy('status'))

    <div class="flex flex-col gap-6">
        {{-- Encabezado del carro --}}
        <section class="flex flex-wrap items-end justify-between gap-6">
            <div class="flex flex-col gap-1.5 min-w-0">
                <span class="eyebrow text-accent-fg">{{ $car->description() ?: 'Mi carro' }}{{ $car->plate ? ' · '.$car->plate : '' }}</span>
                <h1 class="m-0 font-display font-extrabold text-4xl sm:text-[46px] leading-[1.05] tracking-tight text-white break-words">{{ $car->name }}</h1>
            </div>

            <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-1.5" x-data="{ editing: {{ $errors->has('current_km') ? 'true' : 'false' }} }">
                    <span class="eyebrow text-xs text-muted">Odómetro</span>
                    <div x-show="! editing" class="flex items-center gap-2.5">
                        <div role="img" aria-label="{{ number_format($car->current_km, 0, ',', '.') }} kilómetros" class="flex gap-[3px] p-[5px] rounded-lg bg-black border border-line-2">
                            @foreach (str_split(str_pad((string) $car->current_km, 6, '0', STR_PAD_LEFT)) as $digit)
                                <span class="w-[26px] h-9 rounded bg-surface-2 text-accent-fg flex items-center justify-center font-mono font-semibold text-xl">{{ $digit }}</span>
                            @endforeach
                            <span class="ps-1.5 pe-2 flex items-center text-muted font-mono text-[13px]">km</span>
                        </div>
                        <button type="button" @click="editing = true; $nextTick(() => $refs.km.focus())" class="btn-secondary min-h-11">Actualizar</button>
                    </div>
                    <form x-show="editing" x-cloak method="POST" action="{{ route('cars.km') }}" class="flex gap-2">
                        @csrf @method('PATCH')
                        <label for="current_km" class="sr-only">Kilometraje actual</label>
                        <input id="current_km" x-ref="km" type="number" name="current_km" min="0" value="{{ old('current_km', $car->current_km) }}" class="w-40 font-mono">
                        <button class="btn-primary">Guardar</button>
                        <button type="button" @click="editing = false" class="btn-secondary min-h-11">Cancelar</button>
                    </form>
                    <x-input-error :messages="$errors->get('current_km')" />
                </div>
                <a href="{{ route('records.create') }}" class="btn-primary min-h-[46px]">
                    <svg aria-hidden="true" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    Nuevo registro
                </a>
            </div>
        </section>

        {{-- Resumen de estado --}}
        <section aria-label="Estado de mantenimientos" class="grid grid-cols-3 gap-2 sm:gap-3">
            <div class="p-3 sm:px-5 sm:py-4 rounded-xl bg-over-bg border border-over-line flex flex-col sm:flex-row items-center gap-1 sm:gap-3.5 text-center sm:text-start">
                <span class="font-display font-extrabold text-3xl sm:text-[38px] leading-none text-over">{{ $counts['overdue'] ?? 0 }}</span>
                <span class="text-over-soft font-semibold leading-tight">vencidos<span class="hidden sm:block font-normal text-[13px] text-[#E8B3AC]">atiéndelos pronto</span></span>
            </div>
            <div class="p-3 sm:px-5 sm:py-4 rounded-xl bg-soon-bg border border-soon-line flex flex-col sm:flex-row items-center gap-1 sm:gap-3.5 text-center sm:text-start">
                <span class="font-display font-extrabold text-3xl sm:text-[38px] leading-none text-soon">{{ $counts['soon'] ?? 0 }}</span>
                <span class="text-soon-soft font-semibold leading-tight">próximos<span class="hidden sm:block font-normal text-[13px] text-[#E2C98F]">en 30 días o 1.000 km</span></span>
            </div>
            <div class="p-3 sm:px-5 sm:py-4 rounded-xl bg-ok-bg border border-ok-line flex flex-col sm:flex-row items-center gap-1 sm:gap-3.5 text-center sm:text-start">
                <span class="font-display font-extrabold text-3xl sm:text-[38px] leading-none text-ok">{{ $counts['ok'] ?? 0 }}</span>
                <span class="text-ok-soft font-semibold leading-tight">al día<span class="hidden sm:block font-normal text-[13px] text-[#B7D58A]">sin pendientes</span></span>
            </div>
        </section>

        <div class="flex flex-wrap gap-6 items-start">
            {{-- Próximos mantenimientos --}}
            <section class="card overflow-hidden flex-[999_1_560px] min-w-0">
                <div class="px-5 sm:px-[22px] py-[18px] flex flex-wrap items-center justify-between gap-3 border-b border-line">
                    <h2 class="m-0 font-display font-bold text-xl text-white">Próximos mantenimientos</h2>
                    <span class="text-[13px] text-muted">Lo que ocurra primero: km o fecha</span>
                </div>

                @forelse ($items as $item)
                    <article class="px-5 sm:px-[22px] py-[18px] border-b border-line-soft last:border-b-0 grid grid-cols-[minmax(0,1fr)_auto] gap-x-5 gap-y-2.5 items-center">
                        <div class="flex flex-col gap-2 min-w-0">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span @class([
                                    'pill',
                                    'bg-over-bg text-over' => $item->status === 'overdue',
                                    'bg-soon-bg text-soon' => $item->status === 'soon',
                                    'bg-ok-bg text-ok' => $item->status === 'ok',
                                ])>{{ $item->statusLabel() }}</span>
                                <h3 class="m-0 text-base font-semibold text-white">{{ $item->type->name }}</h3>
                                <span class="text-[13px] text-muted">Cada {{ \App\Models\MaintenanceType::formatInterval($item->type->set_km, $item->type->set_months) }}</span>
                            </div>
                            <div role="img" aria-label="{{ $item->progress }}% del intervalo consumido" class="h-1.5 rounded-full bg-line overflow-hidden">
                                <div @class([
                                    'h-full rounded-full',
                                    'bg-over-bar' => $item->status === 'overdue',
                                    'bg-soon-bar' => $item->status === 'soon',
                                    'bg-ok-bar' => $item->status === 'ok',
                                ]) style="width: {{ $item->progress }}%"></div>
                            </div>
                            <div class="flex flex-wrap gap-x-5 gap-y-1 text-[13px] text-muted">
                                <span>Último: <span class="text-fg font-medium">{{ $item->lastRecord->date->format('d/m/Y') }}{{ $item->lastRecord->km !== null ? ' · '.km($item->lastRecord->km) : '' }}</span></span>
                                @if ($item->nextDate || $item->nextKm !== null)
                                    <span>Próximo: <span class="text-fg font-medium">{{ collect([$item->nextDate?->format('d/m/Y'), $item->nextKm !== null ? km($item->nextKm) : null])->filter()->join(' · ') }}</span></span>
                                @endif
                                <span @class([
                                    'font-semibold',
                                    'text-over' => $item->status === 'overdue',
                                    'text-soon' => $item->status === 'soon',
                                    'text-muted' => $item->status === 'ok',
                                ])>{{ $item->remainingLabel() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('records.create', ['type' => $item->type->id]) }}" class="btn-secondary">Registrar</a>
                    </article>
                @empty
                    <div class="px-6 py-12 text-center text-muted">
                        Las alertas aparecen a partir del primer mantenimiento registrado de cada tipo.
                        <div class="mt-4"><a href="{{ route('records.create') }}" class="btn-primary">Registrar el primero</a></div>
                    </div>
                @endforelse
            </section>

            {{-- Columna lateral --}}
            <aside class="flex-[1_1_300px] min-w-0 flex flex-col gap-4">
                <section class="card px-[22px] py-5 flex flex-col gap-3.5">
                    <h2 class="m-0 font-display font-bold text-lg text-white">Gastos</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[13px] text-muted">En {{ now()->year }}</span>
                            <span class="font-mono font-semibold text-[22px] text-accent-fg">{{ money($yearCost) }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[13px] text-muted">Total · {{ $recordsCount }} {{ $recordsCount === 1 ? 'registro' : 'registros' }}</span>
                            <span class="font-mono font-semibold text-[22px] text-white">{{ money($totalCost) }}</span>
                        </div>
                    </div>
                </section>

                <section class="card px-[22px] py-5 flex flex-col gap-1">
                    <div class="flex justify-between items-baseline mb-2">
                        <h2 class="m-0 font-display font-bold text-lg text-white">Historial reciente</h2>
                        @if ($recent->isNotEmpty())
                            <a href="{{ route('records.index') }}" class="text-sm font-semibold no-underline">Ver todo</a>
                        @endif
                    </div>
                    @forelse ($recent as $record)
                        <a href="{{ route('records.edit', $record) }}" class="grid grid-cols-[52px_minmax(0,1fr)_auto] gap-3 items-center py-2.5 border-t border-line-soft text-fg no-underline hover:text-white">
                            <span class="flex flex-col items-center py-1 rounded-md bg-surface-2 leading-tight">
                                <span class="font-bold text-base text-white">{{ $record->date->format('d') }}</span>
                                <span class="text-[11px] uppercase text-muted">{{ $record->date->translatedFormat('M') }}</span>
                            </span>
                            <span class="min-w-0">
                                <span class="block font-semibold text-sm truncate">{{ $record->type->name }}</span>
                                <span class="block text-[13px] text-muted">{{ $record->km !== null ? km($record->km) : $record->date->year }}</span>
                            </span>
                            <span class="font-mono font-medium text-sm">{{ money($record->cost) }}</span>
                        </a>
                    @empty
                        <p class="m-0 text-sm text-muted">Aún no hay registros para este carro.</p>
                    @endforelse
                </section>

                @if ($neverDone->isNotEmpty())
                    <section class="card px-[22px] py-5 flex flex-col gap-3" x-data="{ all: false }">
                        <div>
                            <h2 class="m-0 font-display font-bold text-lg text-white">Sin registrar aún</h2>
                            <p class="mt-1 mb-0 text-[13px] text-muted">Registra el último que hiciste y empezarás a recibir alertas.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($neverDone as $type)
                                <a href="{{ route('records.create', ['type' => $type->id]) }}"
                                   @if ($loop->index >= 6) x-show="all" x-cloak @endif
                                   class="px-3 py-1.5 rounded-full border border-line-2 text-fg no-underline text-[13px] font-medium hover:border-accent hover:text-white">{{ $type->name }}</a>
                            @endforeach
                            @if ($neverDone->count() > 6)
                                <button type="button" x-show="! all" @click="all = true" class="px-3 py-1.5 rounded-full bg-surface-2 text-accent-fg text-[13px] font-semibold">+{{ $neverDone->count() - 6 }} más</button>
                            @endif
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>

    {{-- Botón fijo en móvil --}}
    <a href="{{ route('records.create') }}" class="sm:hidden fixed left-4 right-4 bottom-5 z-40 btn-primary min-h-[54px] text-base shadow-[0_8px_28px_rgba(118,185,0,0.25)]">
        <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo registro
    </a>
    <div class="sm:hidden h-16" aria-hidden="true"></div>
</x-app-layout>
