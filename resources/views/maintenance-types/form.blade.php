<x-app-layout>
    <x-slot name="title">{{ $type->exists ? 'Editar concepto' : 'Nuevo concepto' }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $type->exists ? 'Editar concepto' : 'Nuevo concepto' }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ $type->exists ? route('maintenance-types.update', $type) : route('maintenance-types.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            @csrf
            @if ($type->exists) @method('PUT') @endif

            <div>
                <x-input-label for="name" value="Nombre *" />
                <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $type->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="category" value="Categoría *" />
                <x-text-input id="category" name="category" list="categories" class="mt-1 block w-full" :value="old('category', $type->category)" required />
                <datalist id="categories">
                    @foreach ($categories as $category) <option value="{{ $category }}"> @endforeach
                </datalist>
                <x-input-error :messages="$errors->get('category')" class="mt-1" />
            </div>

            <fieldset class="rounded-md border border-gray-200 p-4">
                <legend class="px-1 text-sm font-medium text-gray-700">Intervalo seteado para alertas (lo que ocurra primero)</legend>
                @if ($type->recommended_km !== null || $type->recommended_months !== null)
                    <p class="text-xs text-gray-500 mb-3">Recomendado: {{ \App\Models\MaintenanceType::formatInterval($type->recommended_km, $type->recommended_months) }}</p>
                @endif
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="set_km" value="Cada (km)" />
                        <x-text-input id="set_km" name="set_km" type="number" min="1" class="mt-1 block w-full" :value="old('set_km', $type->set_km)" placeholder="Sin límite por km" />
                        <x-input-error :messages="$errors->get('set_km')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="set_months" value="Cada (meses)" />
                        <x-text-input id="set_months" name="set_months" type="number" min="1" class="mt-1 block w-full" :value="old('set_months', $type->set_months)" placeholder="Sin límite por tiempo" />
                        <x-input-error :messages="$errors->get('set_months')" class="mt-1" />
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Deja ambos vacíos si no quieres alertas para este concepto.</p>
            </fieldset>

            <div>
                <x-input-label for="notes" value="Notas" />
                <x-text-input id="notes" name="notes" class="mt-1 block w-full" :value="old('notes', $type->notes)" />
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $type->is_active))>
                Activo (aparece en el selector y en las alertas)
            </label>

            <div class="flex items-center justify-between pt-2">
                <x-primary-button>Guardar</x-primary-button>
                <a href="{{ route('maintenance-types.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>

        @if ($type->exists)
            <form method="POST" action="{{ route('maintenance-types.destroy', $type) }}" class="mt-6" onsubmit="return confirm('¿Eliminar este concepto del catálogo?')">
                @csrf @method('DELETE')
                <x-danger-button>Eliminar concepto</x-danger-button>
                <p class="text-xs text-gray-500 mt-1">Solo se puede eliminar si no tiene registros en ningún carro; si no, desactívalo.</p>
            </form>
        @endif
    </div>
</x-app-layout>
