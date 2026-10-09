<x-app-layout>
    <x-slot name="title">{{ $car->exists ? 'Editar carro' : 'Nuevo carro' }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $car->exists ? 'Editar '.$car->name : 'Nuevo carro' }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        @if ($first)
            <div class="mb-4 rounded-md bg-indigo-50 border border-indigo-200 px-4 py-3 text-sm text-indigo-800">
                ¡Bienvenido a MemoCar! Empieza registrando tu primer carro.
            </div>
        @endif

        <form method="POST" action="{{ $car->exists ? route('cars.update', $car) : route('cars.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            @csrf
            @if ($car->exists) @method('PUT') @endif

            <div>
                <x-input-label for="name" value="Nombre o alias *" />
                <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $car->name)" required autofocus placeholder="Ej: El Corolla de papá" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="brand" value="Marca" />
                    <x-text-input id="brand" name="brand" class="mt-1 block w-full" :value="old('brand', $car->brand)" />
                    <x-input-error :messages="$errors->get('brand')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="model" value="Modelo" />
                    <x-text-input id="model" name="model" class="mt-1 block w-full" :value="old('model', $car->model)" />
                    <x-input-error :messages="$errors->get('model')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="year" value="Año" />
                    <x-text-input id="year" name="year" type="number" class="mt-1 block w-full" :value="old('year', $car->year)" />
                    <x-input-error :messages="$errors->get('year')" class="mt-1" />
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="plate" value="Placa" />
                    <x-text-input id="plate" name="plate" class="mt-1 block w-full" :value="old('plate', $car->plate)" />
                    <x-input-error :messages="$errors->get('plate')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="current_km" value="Kilometraje actual" />
                    <x-text-input id="current_km" name="current_km" type="number" min="0" class="mt-1 block w-full" :value="old('current_km', $car->current_km)" />
                    <x-input-error :messages="$errors->get('current_km')" class="mt-1" />
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <x-primary-button>Guardar</x-primary-button>
                @unless ($first)
                    <a href="{{ route('cars.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                @endunless
            </div>
        </form>

        @if ($car->exists)
            <form method="POST" action="{{ route('cars.destroy', $car) }}" class="mt-6" onsubmit="return confirm('¿Eliminar este carro y todo su historial? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <x-danger-button>Eliminar carro</x-danger-button>
            </form>
        @endif
    </div>
</x-app-layout>
