<x-app-layout>
    <x-slot name="title">Mis carros</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Mis carros</h2>
            <a href="{{ route('cars.create') }}"><x-primary-button type="button">Agregar carro</x-primary-button></a>
        </div>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Carro</th>
                    <th class="px-4 py-3">Placa</th>
                    <th class="px-4 py-3 text-right">Kilometraje</th>
                    <th class="px-4 py-3 text-right">Registros</th>
                    <th class="px-4 py-3 text-right">Gastado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($cars as $car)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $car->name }}</span>
                            @if ($car->id === $selectedId)
                                <span class="ms-1 rounded bg-indigo-100 px-1.5 py-0.5 text-xs text-indigo-700">seleccionado</span>
                            @endif
                            <div class="text-gray-500">{{ $car->description() }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $car->plate ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">{{ km($car->current_km) }}</td>
                        <td class="px-4 py-3 text-right">{{ $car->records_count }}</td>
                        <td class="px-4 py-3 text-right">{{ money($car->records_sum_cost) }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <form method="POST" action="{{ route('cars.choose', $car) }}" class="inline">
                                @csrf
                                <button class="text-indigo-600 hover:underline">Ver</button>
                            </form>
                            <a href="{{ route('cars.edit', $car) }}" class="ms-3 text-gray-600 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Aún no tienes carros registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
