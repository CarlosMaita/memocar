<x-app-layout>
    <x-slot name="title">Catálogo</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <span class="eyebrow text-accent-fg">Fallas y mantenimientos</span>
                <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">Catálogo</h1>
                <p class="mt-2 mb-0 text-muted">El intervalo <span class="text-white font-semibold">seteado</span> es el que usa MemoCar para las alertas; el <span class="text-white font-semibold">recomendado</span> es la referencia común. Aplica a todos tus carros.</p>
            </div>
            <a href="{{ route('maintenance-types.create') }}" class="btn-primary">
                <svg aria-hidden="true" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Agregar concepto
            </a>
        </div>
    </x-slot>

    <div class="flex flex-col gap-6">
        @foreach ($typesByCategory as $category => $types)
            <section class="card overflow-x-auto">
                <h2 class="m-0 px-5 py-3 border-b border-line font-display font-bold text-lg text-white">{{ $category }}</h2>
                <table class="min-w-full text-sm">
                    <thead class="text-muted text-left">
                        <tr>
                            <th class="px-5 py-2.5 font-semibold w-1/2">Concepto</th>
                            <th class="px-5 py-2.5 font-semibold">Recomendado</th>
                            <th class="px-5 py-2.5 font-semibold">Seteado (alerta)</th>
                            <th class="px-5 py-2.5 font-semibold text-right whitespace-nowrap">En {{ $currentCar->name }}</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $type)
                            @php($custom = $type->set_km !== $type->recommended_km || $type->set_months !== $type->recommended_months)
                            <tr @class(['border-t border-line-soft', 'opacity-50' => ! $type->is_active])>
                                <td class="px-5 py-3">
                                    <span class="text-white font-medium">{{ $type->name }}</span>
                                    @unless ($type->is_active) <span class="ms-1 pill bg-surface-2 text-muted">inactivo</span> @endunless
                                    @if ($type->notes) <div class="text-xs text-muted mt-0.5">{{ $type->notes }}</div> @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-muted">{{ \App\Models\MaintenanceType::formatInterval($type->recommended_km, $type->recommended_months) }}</td>
                                <td @class(['px-5 py-3 whitespace-nowrap', 'font-semibold text-accent-fg' => $custom, 'text-fg' => ! $custom])>
                                    {{ \App\Models\MaintenanceType::formatInterval($type->set_km, $type->set_months) }}
                                </td>
                                <td class="px-5 py-3 text-right">{{ $type->records_count }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('maintenance-types.edit', $type) }}" class="font-semibold no-underline">Editar</a>
                                    @if ($custom && ($type->recommended_km !== null || $type->recommended_months !== null))
                                        <form method="POST" action="{{ route('maintenance-types.reset', $type) }}" class="inline ms-3">
                                            @csrf
                                            <button class="text-muted hover:text-white" title="Volver al intervalo recomendado">Restablecer</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach
    </div>
</x-app-layout>
