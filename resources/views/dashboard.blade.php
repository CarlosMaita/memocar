<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $car->name }}</h2>
                <p class="text-sm text-gray-500">{{ $car->description() }}{{ $car->plate ? ' · '.$car->plate : '' }}</p>
            </div>
            <a href="{{ route('records.create') }}"><x-primary-button type="button">+ Nuevo registro</x-primary-button></a>
        </div>
    </x-slot>

    {{-- KPIs --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow-sm p-4" x-data="{ editing: false }">
            <div class="text-xs uppercase tracking-wide text-gray-500">Kilometraje actual</div>
            <div x-show="!editing" class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl font-semibold">{{ km($car->current_km) }}</span>
                <button type="button" @click="editing = true" class="text-xs text-indigo-600 hover:underline">actualizar</button>
            </div>
            <form x-show="editing" x-cloak method="POST" action="{{ route('cars.km') }}" class="mt-1 flex gap-2">
                @csrf @method('PATCH')
                <input type="number" name="current_km" min="0" value="{{ $car->current_km }}" class="w-full rounded-md border-gray-300 text-sm">
                <button class="rounded-md bg-indigo-600 px-3 text-sm text-white">OK</button>
            </form>
            <x-input-error :messages="$errors->get('current_km')" class="mt-1" />
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Gastado en {{ now()->year }}</div>
            <div class="mt-1 text-2xl font-semibold">{{ money($yearCost) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Gasto total</div>
            <div class="mt-1 text-2xl font-semibold">{{ money($totalCost) }}</div>
            <div class="text-xs text-gray-500">{{ $recordsCount }} {{ $recordsCount === 1 ? 'registro' : 'registros' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Último servicio</div>
            @if ($lastRecord)
                <div class="mt-1 font-semibold leading-tight">{{ $lastRecord->type->name }}</div>
                <div class="text-xs text-gray-500">{{ $lastRecord->date->format('d/m/Y') }} · {{ $lastRecord->date->diffForHumans() }}</div>
            @else
                <div class="mt-1 text-gray-400">Sin registros</div>
            @endif
        </div>
    </div>

    {{-- Próximos mantenimientos --}}
    @php($counts = $items->countBy('status'))
    <div class="mt-6 bg-white rounded-lg shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-semibold">Próximos mantenimientos</h3>
            <div class="flex gap-2 text-xs">
                <span class="rounded-full bg-red-100 text-red-700 px-2 py-0.5">{{ $counts['overdue'] ?? 0 }} vencidos</span>
                <span class="rounded-full bg-amber-100 text-amber-700 px-2 py-0.5">{{ $counts['soon'] ?? 0 }} próximos</span>
                <span class="rounded-full bg-green-100 text-green-700 px-2 py-0.5">{{ $counts['ok'] ?? 0 }} al día</span>
            </div>
        </div>

        @if ($items->isEmpty())
            <div class="px-4 py-10 text-center text-gray-500">
                Las alertas aparecen a partir del primer mantenimiento registrado de cada tipo.
                <div class="mt-3"><a href="{{ route('records.create') }}" class="text-indigo-600 hover:underline">Registra el primero</a></div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Mantenimiento</th>
                            <th class="px-4 py-2">Último</th>
                            <th class="px-4 py-2">Próximo</th>
                            <th class="px-4 py-2">Falta</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap',
                                        'bg-red-100 text-red-700' => $item->status === 'overdue',
                                        'bg-amber-100 text-amber-700' => $item->status === 'soon',
                                        'bg-green-100 text-green-700' => $item->status === 'ok',
                                    ])>{{ $item->statusLabel() }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->type->name }}</div>
                                    <div class="text-xs text-gray-500">Cada {{ \App\Models\MaintenanceType::formatInterval($item->type->set_km, $item->type->set_months) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $item->lastRecord->date->format('d/m/Y') }}
                                    <div class="text-xs text-gray-500">{{ km($item->lastRecord->km) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $item->nextDate?->format('d/m/Y') ?? '—' }}
                                    <div class="text-xs text-gray-500">{{ km($item->nextKm) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($item->daysLeft !== null)
                                        <div @class(['text-red-600' => $item->daysLeft < 0])>
                                            {{ $item->daysLeft < 0 ? 'Hace '.abs($item->daysLeft).' días' : $item->daysLeft.' días' }}
                                        </div>
                                    @endif
                                    @if ($item->kmLeft !== null)
                                        <div @class(['text-xs', 'text-red-600' => $item->kmLeft < 0, 'text-gray-500' => $item->kmLeft >= 0])>
                                            {{ $item->kmLeft < 0 ? 'Pasado por '.km(abs($item->kmLeft)) : km($item->kmLeft) }}
                                        </div>
                                    @endif
                                    @if ($item->daysLeft === null && $item->kmLeft === null)
                                        <span class="text-xs text-gray-400">Registra el km para calcular</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('records.create', ['type' => $item->type->id]) }}" class="text-indigo-600 hover:underline whitespace-nowrap">Registrar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($neverDone->isNotEmpty())
        <div class="mt-6 bg-white rounded-lg shadow-sm" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="w-full px-4 py-3 flex items-center justify-between text-start">
                <span class="font-semibold">Sin registrar aún <span class="text-gray-400 font-normal">({{ $neverDone->count() }})</span></span>
                <span class="text-sm text-gray-500" x-text="open ? 'Ocultar' : 'Ver'"></span>
            </button>
            <div x-show="open" x-cloak class="border-t border-gray-100 px-4 py-3">
                <p class="text-sm text-gray-500 mb-3">Si ya hiciste alguno de estos mantenimientos, regístralo para empezar a recibir alertas.</p>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                    @foreach ($neverDone as $type)
                        <a href="{{ route('records.create', ['type' => $type->id]) }}" class="rounded-md border border-gray-200 px-3 py-2 hover:border-indigo-400">
                            {{ $type->name }}
                            <span class="block text-xs text-gray-500">Cada {{ \App\Models\MaintenanceType::formatInterval($type->set_km, $type->set_months) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
