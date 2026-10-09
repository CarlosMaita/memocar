<x-app-layout>
    <x-slot name="title">Seleccionar carro</x-slot>

    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold">¿Qué carro vas a revisar?</h1>
        <p class="text-gray-500 mt-1">El dashboard, los registros y las alertas se muestran por el carro que elijas.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ($cars as $car)
                <form method="POST" action="{{ route('cars.choose', $car) }}">
                    @csrf
                    <button class="w-full text-start bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-indigo-400 hover:shadow transition">
                        <div class="flex items-center gap-3">
                            <x-application-logo class="h-10 w-10 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold text-lg">{{ $car->name }}</div>
                                <div class="text-sm text-gray-500">{{ $car->description() ?: 'Sin detalles' }}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between text-sm text-gray-600">
                            <span>{{ km($car->current_km) }}</span>
                            <span>{{ $car->records_count }} {{ $car->records_count === 1 ? 'registro' : 'registros' }}</span>
                        </div>
                    </button>
                </form>
            @endforeach

            <a href="{{ route('cars.create') }}" class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-5 text-gray-500 hover:border-indigo-400 hover:text-indigo-600">
                + Agregar otro carro
            </a>
        </div>
    </div>
</x-app-layout>
