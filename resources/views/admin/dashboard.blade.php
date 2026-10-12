<x-app-layout>
    <x-slot name="title">Admin</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow text-accent-fg">Administración</span>
                <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">Estadísticas de MemoCar</h1>
                <p class="mt-2 mb-0 text-muted">Datos de todos los usuarios. {{ $includeDemo ? 'Incluye' : 'Excluye' }} las cuentas demo ({{ $kpis['demoActive'] }} activas ahora).</p>
            </div>
            <nav aria-label="Origen de los datos" class="flex gap-1.5">
                @foreach ([false => 'Solo usuarios reales', true => 'Incluir demo'] as $value => $label)
                    <a href="{{ route('admin', $value ? ['demo' => 1] : []) }}" @if ($includeDemo === (bool) $value) aria-current="true" @endif
                       @class([
                           'min-h-[42px] px-3.5 rounded-lg border flex items-center text-sm no-underline',
                           'border-accent bg-accent text-on-accent! font-bold' => $includeDemo === (bool) $value,
                           'border-line-2 bg-bg text-fg! font-semibold hover:border-muted' => $includeDemo !== (bool) $value,
                       ])>{{ $label }}</a>
                @endforeach
            </nav>
        </div>
    </x-slot>

    @php($maxSpectrum = max(1, $spectrum->max('count') ?? 1))
    @php($topSpectrum = $spectrum->take(15))
    @php($otherSpectrum = $spectrum->slice(15))
    @php($pareto = $spectrum->search(fn ($row) => $row['cumulative'] >= 80))

    <div class="flex flex-col gap-6">
        {{-- KPIs --}}
        <section aria-label="Totales" class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="card p-5">
                <div class="text-[13px] text-muted">Usuarios registrados</div>
                <div class="font-display font-extrabold text-4xl text-white leading-tight">{{ number_format($kpis['users'], 0, ',', '.') }}</div>
                <div class="text-[13px] text-muted">+{{ $kpis['usersLast30'] }} en 30 días · {{ $kpis['usersWithCars'] }} con carros</div>
            </div>
            <div class="card p-5">
                <div class="text-[13px] text-muted">Carros registrados</div>
                <div class="font-display font-extrabold text-4xl text-white leading-tight">{{ number_format($kpis['cars'], 0, ',', '.') }}</div>
                <div class="text-[13px] text-muted">{{ number_format($kpis['cars'] / max(1, $kpis['carOwners']), 1, ',', '.') }} por usuario con carros</div>
            </div>
            <div class="card p-5">
                <div class="text-[13px] text-muted">Registros cargados</div>
                <div class="font-display font-extrabold text-4xl text-white leading-tight">{{ number_format($kpis['records'], 0, ',', '.') }}</div>
                <div class="text-[13px] text-muted">+{{ $kpis['recordsLast30'] }} en 30 días</div>
            </div>
            <div class="card p-5">
                <div class="text-[13px] text-muted">Gasto registrado</div>
                <div class="font-mono font-semibold text-3xl text-accent-fg leading-tight mt-1">{{ money($kpis['cost']) }}</div>
                <div class="text-[13px] text-muted">{{ $kpis['records'] ? money($kpis['cost'] / $kpis['records']).' por registro' : 'Sin registros' }}</div>
            </div>
        </section>

        {{-- Registros cargados por mes --}}
        @php($maxMonth = max(1, $perMonth->max('count')))
        <section class="card p-5">
            <div class="flex flex-wrap items-baseline justify-between gap-2 mb-4">
                <h2 class="m-0 font-display font-bold text-xl text-white">Registros cargados por mes</h2>
                <span class="text-[13px] text-muted">Últimos 12 meses, por fecha de carga</span>
            </div>
            <div class="flex items-end gap-1.5 sm:gap-2 h-44 border-b border-line" role="img" aria-label="Registros cargados por mes: {{ $perMonth->map(fn ($m) => $m['month']->translatedFormat('M Y').' '.$m['count'])->join(', ') }}">
                @foreach ($perMonth as $m)
                    <div class="group relative flex-1 h-full flex flex-col justify-end items-center">
                        <span class="text-[11px] font-mono text-muted mb-1 {{ $m['count'] ? '' : 'invisible' }}">{{ $m['count'] }}</span>
                        <div class="w-full max-w-10 rounded-t bg-accent group-hover:bg-accent-hover" style="height: {{ $m['count'] ? max(2, round($m['count'] / $maxMonth * 100)) : 0 }}%"></div>
                        <span class="pointer-events-none absolute -top-2 left-1/2 -translate-x-1/2 -translate-y-full whitespace-nowrap rounded-md border border-line-2 bg-bar px-2 py-1 text-xs text-fg opacity-0 group-hover:opacity-100">{{ ucfirst($m['month']->translatedFormat('F Y')) }}: {{ $m['count'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-1.5 sm:gap-2 mt-1.5">
                @foreach ($perMonth as $m)
                    <span class="flex-1 text-center text-[11px] uppercase text-muted">{{ $m['month']->translatedFormat('M') }}</span>
                @endforeach
            </div>
        </section>

        {{-- Espectro de fallas --}}
        <section class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-line flex flex-wrap items-baseline justify-between gap-2">
                <div>
                    <h2 class="m-0 font-display font-bold text-xl text-white">Espectro de fallas y mantenimientos</h2>
                    <p class="mt-1 mb-0 text-[13px] text-muted">Qué se registra con más frecuencia. Barra = número de registros; % = participación sobre el total.</p>
                </div>
                @if ($pareto !== false)
                    <span class="pill bg-ok-bg text-ok">{{ $pareto + 1 }} de {{ $spectrum->count() }} conceptos concentran el 80%</span>
                @endif
            </div>

            @if ($spectrum->isEmpty())
                <p class="m-0 px-5 py-10 text-center text-muted">Todavía no hay registros cargados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-muted text-left">
                            <tr>
                                <th class="px-5 py-2.5 font-semibold w-10">#</th>
                                <th class="px-5 py-2.5 font-semibold">Concepto</th>
                                <th class="px-5 py-2.5 font-semibold min-w-[220px] w-2/5">Frecuencia</th>
                                <th class="px-5 py-2.5 font-semibold text-right">%</th>
                                <th class="px-5 py-2.5 font-semibold text-right whitespace-nowrap">% acum.</th>
                                <th class="px-5 py-2.5 font-semibold text-right whitespace-nowrap">Costo prom.</th>
                                <th class="px-5 py-2.5 font-semibold text-right">Carros</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topSpectrum as $i => $row)
                                <tr class="border-t border-line-soft group hover:bg-surface-2" title="{{ $row['name'] }}: {{ $row['count'] }} registros ({{ number_format($row['share'], 1, ',', '.') }}%)">
                                    <td class="px-5 py-2.5 font-mono text-muted">{{ $i + 1 }}</td>
                                    <td class="px-5 py-2.5">
                                        <span class="text-white font-medium">{{ $row['name'] }}</span>
                                        <span class="ms-1.5 px-1.5 py-px rounded bg-surface-2 text-xs text-muted">{{ $row['category'] }}</span>
                                        @if ($pareto !== false && $i <= $pareto)
                                            <span class="ms-1 text-[11px] font-semibold text-accent-fg" title="Dentro del 80% de los registros">80%</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex-1 h-2.5 rounded-full bg-line overflow-hidden">
                                                <div class="h-full rounded-full bg-accent" style="width: {{ max(2, round($row['count'] / $maxSpectrum * 100)) }}%"></div>
                                            </div>
                                            <span class="w-10 text-right font-mono font-semibold text-white">{{ $row['count'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-2.5 text-right font-mono">{{ number_format($row['share'], 1, ',', '.') }}</td>
                                    <td class="px-5 py-2.5 text-right font-mono text-muted">{{ number_format($row['cumulative'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-2.5 text-right font-mono">{{ money($row['avgCost']) }}</td>
                                    <td class="px-5 py-2.5 text-right font-mono">{{ $row['cars'] }}</td>
                                </tr>
                            @endforeach
                            @if ($otherSpectrum->isNotEmpty())
                                <tr class="border-t border-line-soft">
                                    <td class="px-5 py-2.5"></td>
                                    <td class="px-5 py-2.5 text-muted">Otros {{ $otherSpectrum->count() }} conceptos</td>
                                    <td class="px-5 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex-1 h-2.5 rounded-full bg-line overflow-hidden">
                                                <div class="h-full rounded-full bg-muted" style="width: {{ min(100, max(2, round($otherSpectrum->sum('count') / $maxSpectrum * 100))) }}%"></div>
                                            </div>
                                            <span class="w-10 text-right font-mono font-semibold text-white">{{ $otherSpectrum->sum('count') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-2.5 text-right font-mono">{{ number_format($otherSpectrum->sum('share'), 1, ',', '.') }}</td>
                                    <td class="px-5 py-2.5 text-right font-mono text-muted">100</td>
                                    <td class="px-5 py-2.5"></td>
                                    <td class="px-5 py-2.5"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Espectrograma: categoría × mes --}}
        <section class="card p-5">
            <div class="flex flex-wrap items-baseline justify-between gap-2 mb-4">
                <div>
                    <h2 class="m-0 font-display font-bold text-xl text-white">Espectrograma por categoría</h2>
                    <p class="mt-1 mb-0 text-[13px] text-muted">Servicios realizados por mes (fecha del servicio). Más intenso = más registros.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-muted" aria-hidden="true">
                    0
                    <span class="flex">
                        @foreach ([0, 0.25, 0.5, 0.75, 1] as $step)
                            <span class="w-5 h-3 first:rounded-s last:rounded-e" style="background: {{ $step ? 'rgb(118 185 0 / '.(0.15 + 0.85 * $step).')' : 'var(--color-surface-2)' }}"></span>
                        @endforeach
                    </span>
                    {{ $spectrogram['max'] }}
                </div>
            </div>

            @if ($spectrogram['rows']->isEmpty())
                <p class="m-0 py-8 text-center text-muted">Sin servicios en los últimos 12 meses.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-separate border-spacing-[3px]">
                        <thead>
                            <tr>
                                <th class="text-left font-semibold text-muted pe-3"><span class="sr-only">Categoría</span></th>
                                @foreach ($spectrogram['months'] as $month)
                                    <th class="font-semibold text-muted uppercase min-w-9">{{ $month->translatedFormat('M') }}<span class="block font-normal normal-case text-[10px]">{{ $month->format('y') }}</span></th>
                                @endforeach
                                <th class="font-semibold text-muted ps-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spectrogram['rows'] as $row)
                                <tr>
                                    <th scope="row" class="text-left font-medium text-fg-2 pe-3 whitespace-nowrap text-sm">{{ $row['category'] }}</th>
                                    @foreach ($row['cells'] as $i => $value)
                                        @php($level = $value / $spectrogram['max'])
                                        <td class="h-9 rounded text-center font-mono font-semibold {{ $level > 0.5 ? 'text-on-accent' : 'text-fg' }}"
                                            style="background: {{ $value ? 'rgb(118 185 0 / '.round(0.15 + 0.85 * $level, 2).')' : 'var(--color-surface-2)' }}"
                                            title="{{ $row['category'] }} · {{ ucfirst($spectrogram['months'][$i]->translatedFormat('F Y')) }}: {{ $value }}">{{ $value ?: '' }}</td>
                                    @endforeach
                                    <td class="ps-2 text-right font-mono font-semibold text-white text-sm">{{ $row['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="flex flex-wrap gap-6 items-start">
            {{-- Por categoría --}}
            @php($maxCategory = max(1, $byCategory->max('count') ?? 1))
            <section class="card p-5 flex-[1_1_360px] min-w-0">
                <h2 class="m-0 mb-4 font-display font-bold text-xl text-white">Registros por categoría</h2>
                <div class="flex flex-col gap-3">
                    @forelse ($byCategory as $row)
                        <div title="{{ $row['category'] }}: {{ $row['count'] }}">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-fg-2">{{ $row['category'] }}</span>
                                <span class="font-mono font-semibold text-white">{{ $row['count'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-line overflow-hidden">
                                <div class="h-full rounded-full bg-accent" style="width: {{ max(2, round($row['count'] / $maxCategory * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="m-0 text-muted text-sm">Sin datos.</p>
                    @endforelse
                </div>
            </section>

            {{-- Últimos usuarios --}}
            <section class="card overflow-hidden flex-[1_1_420px] min-w-0">
                <h2 class="m-0 px-5 py-4 border-b border-line font-display font-bold text-xl text-white">Últimos usuarios</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-muted text-left">
                            <tr>
                                <th class="px-5 py-2.5 font-semibold">Usuario</th>
                                <th class="px-5 py-2.5 font-semibold text-right">Carros</th>
                                <th class="px-5 py-2.5 font-semibold text-right">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestUsers as $user)
                                <tr class="border-t border-line-soft">
                                    <td class="px-5 py-2.5">
                                        <span class="text-white font-medium">{{ $user->name }}</span>
                                        @if ($user->is_admin) <span class="ms-1 pill bg-surface-2 text-accent-fg">admin</span> @endif
                                        <div class="text-xs text-muted">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-5 py-2.5 text-right font-mono">{{ $user->cars_count }}</td>
                                    <td class="px-5 py-2.5 text-right text-muted whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-muted">Sin usuarios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
