<x-app-layout>
    <x-slot name="title">{{ $record->exists ? 'Editar registro' : 'Nuevo registro' }}</x-slot>
    <x-slot name="header">
        <span class="eyebrow text-accent-fg">{{ $currentCar->name }}</span>
        <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">{{ $record->exists ? 'Editar registro' : 'Nuevo registro' }}</h1>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ $record->exists ? route('records.update', $record) : route('records.store') }}" class="card p-6 space-y-5">
            @csrf
            @if ($record->exists) @method('PUT') @endif

            <div>
                <x-input-label for="maintenance_type_id" value="Concepto (falla o mantenimiento) *" />
                <select id="maintenance_type_id" name="maintenance_type_id" required class="mt-1 block w-full min-h-[46px]">
                    <option value="">Selecciona…</option>
                    @foreach ($typesByCategory as $category => $types)
                        <optgroup label="{{ $category }}">
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('maintenance_type_id', $record->maintenance_type_id) == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-muted">¿No está en la lista? <a href="{{ route('maintenance-types.create') }}" class="font-semibold">Agrégalo al catálogo</a>.</p>
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
                    <x-text-input id="km" name="km" type="number" min="0" class="mt-1 block w-full font-mono" :value="old('km', $record->km)" />
                    <x-input-error :messages="$errors->get('km')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="cost" value="Costo (USD) *" />
                    <x-text-input id="cost" name="cost" type="number" step="0.01" min="0" class="mt-1 block w-full font-mono" :value="old('cost', $record->cost)" required />
                    <x-input-error :messages="$errors->get('cost')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="description" value="Descripción (opcional)" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full min-h-[46px]" placeholder="Taller, repuestos usados, observaciones…">{{ old('description', $record->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <x-primary-button>Guardar</x-primary-button>
                <a href="{{ route('records.index') }}" class="text-sm text-muted hover:text-white">Cancelar</a>
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
