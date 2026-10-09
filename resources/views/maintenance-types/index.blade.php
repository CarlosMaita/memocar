<x-app-layout>
    <x-slot name="title">Catálogo</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Catálogo de fallas y mantenimientos</h2>
                <p class="text-sm text-gray-500">El intervalo <b>seteado</b> es el que usa MemoCar para las alertas; el <b>recomendado</b> es la referencia común. Aplica a todos tus carros.</p>
            </div>
            <a href="{{ route('maintenance-types.create') }}"><x-primary-button type="button">+ Agregar concepto</x-primary-button></a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @foreach ($typesByCategory as $category => $types)
            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <div class="px-4 py-2 bg-gray-50 font-semibold text-gray-700">{{ $category }}</div>
                <table class="min-w-full text-sm">
                    <thead class="text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-2 w-1/2">Concepto</th>
                            <th class="px-4 py-2">Recomendado</th>
                            <th class="px-4 py-2">Seteado (alerta)</th>
                            <th class="px-4 py-2 text-right">Registros en {{ $currentCar->name }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($types as $type)
                            @php($custom = $type->set_km !== $type->recommended_km || $type->set_months !== $type->recommended_months)
                            <tr @class(['opacity-50' => ! $type->is_active])>
                                <td class="px-4 py-2">
                                    {{ $type->name }}
                                    @unless ($type->is_active) <span class="text-xs text-gray-500">(inactivo)</span> @endunless
                                    @if ($type->notes) <div class="text-xs text-gray-500">{{ $type->notes }}</div> @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-gray-500">{{ \App\Models\MaintenanceType::formatInterval($type->recommended_km, $type->recommended_months) }}</td>
                                <td @class(['px-4 py-2 whitespace-nowrap', 'font-semibold text-indigo-700' => $custom])>
                                    {{ \App\Models\MaintenanceType::formatInterval($type->set_km, $type->set_months) }}
                                </td>
                                <td class="px-4 py-2 text-right">{{ $type->records_count }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('maintenance-types.edit', $type) }}" class="text-indigo-600 hover:underline">Editar</a>
                                    @if ($custom && ($type->recommended_km !== null || $type->recommended_months !== null))
                                        <form method="POST" action="{{ route('maintenance-types.reset', $type) }}" class="inline ms-2">
                                            @csrf
                                            <button class="text-gray-500 hover:underline" title="Volver al intervalo recomendado">Restablecer</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-app-layout>
