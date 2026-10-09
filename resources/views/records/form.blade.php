<x-app-layout>
    <x-slot name="title">{{ $record->exists ? 'Editar registro' : 'Nuevo registro' }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $record->exists ? 'Editar registro' : 'Nuevo registro' }} · {{ $currentCar->name }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ $record->exists ? route('records.update', $record) : route('records.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            @csrf
            @if ($record->exists) @method('PUT') @endif

            <div>
                <x-input-label for="maintenance_type_id" value="Concepto (falla o mantenimiento) *" />
                <select id="maintenance_type_id" name="maintenance_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Selecciona…</option>
                    @foreach ($typesByCategory as $category => $types)
                        <optgroup label="{{ $category }}">
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('maintenance_type_id', $record->maintenance_type_id) == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">¿No está en la lista? <a href="{{ route('maintenance-types.create') }}" class="text-indigo-600 hover:underline">Agrégalo al catálogo</a>.</p>
                <x-input-error :messages="$errors->get('maintenance_type_id')" class="mt-1" />
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="date" value="Fecha *" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $record->date?->format('Y-m-d'))" max="{{ now()->toDateString() }}" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="km" value="Kilometraje" />
                    <x-text-input id="km" name="km" type="number" min="0" class="mt-1 block w-full" :value="old('km', $record->km)" />
                    <x-input-error :messages="$errors->get('km')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="cost" value="Costo (USD) *" />
                    <x-text-input id="cost" name="cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('cost', $record->cost)" required />
                    <x-input-error :messages="$errors->get('cost')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="description" value="Descripción (opcional)" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Taller, repuestos usados, observaciones…">{{ old('description', $record->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <x-primary-button>Guardar</x-primary-button>
                <a href="{{ route('records.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>

        @if ($record->exists)
            <form method="POST" action="{{ route('records.destroy', $record) }}" class="mt-6" onsubmit="return confirm('¿Eliminar este registro?')">
                @csrf @method('DELETE')
                <x-danger-button>Eliminar registro</x-danger-button>
            </form>
        @endif
    </div>
</x-app-layout>
