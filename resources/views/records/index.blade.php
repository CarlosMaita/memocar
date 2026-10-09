<x-app-layout>
    <x-slot name="title">Registros</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800">Registros de {{ $currentCar->name }}</h2>
            <a href="{{ route('records.create') }}"><x-primary-button type="button">+ Nuevo registro</x-primary-button></a>
        </div>
    </x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1" for="type">Concepto</label>
            <select id="type" name="type" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected(($filters['type'] ?? null) == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1" for="year">Año</label>
            <select id="year" name="year" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" @selected(($filters['year'] ?? null) == $year)>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        @if (array_filter($filters))
            <a href="{{ route('records.index') }}" class="text-sm text-gray-600 hover:underline pb-2">Limpiar filtros</a>
        @endif
        <div class="ms-auto text-sm pb-2">Total: <span class="font-semibold">{{ money($total) }}</span></div>
    </form>

    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Concepto</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3 text-right">Km</th>
                    <th class="px-4 py-3 text-right">Costo</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($records as $record)
                    <tr class="align-top">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $record->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $record->type->name }}</span>
                            <div class="text-xs text-gray-500">{{ $record->type->category }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-md">{{ $record->description ?: '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">{{ km($record->km) }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">{{ money($record->cost) }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('records.edit', $record) }}" class="text-indigo-600 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">No hay registros {{ array_filter($filters) ? 'con estos filtros' : 'para este carro' }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>
</x-app-layout>
