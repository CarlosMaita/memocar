@php($navCars = Auth::user()->cars()->orderBy('name')->get(['id', 'name', 'brand', 'model', 'year']))
<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center gap-2">
                    <x-application-logo class="h-8 w-8 text-indigo-600" />
                    <span class="font-semibold text-lg tracking-tight">MemoCar</span>
                </a>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('records.index')" :active="request()->routeIs('records.*')">Registros</x-nav-link>
                    <x-nav-link :href="route('maintenance-types.index')" :active="request()->routeIs('maintenance-types.*')">Catálogo</x-nav-link>
                    <x-nav-link :href="route('cars.index')" :active="request()->routeIs('cars.index', 'cars.create', 'cars.edit')">Mis carros</x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                {{-- Selector de carro --}}
                @if ($navCars->isNotEmpty())
                    <x-dropdown align="right" width="w-64">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                <x-application-logo class="h-4 w-4" />
                                <span>{{ isset($currentCar) ? $currentCar->name : 'Seleccionar carro' }}</span>
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide">Cambiar de carro</div>
                            @foreach ($navCars as $navCar)
                                <form method="POST" action="{{ route('cars.choose', $navCar) }}">
                                    @csrf
                                    <button class="w-full text-start px-4 py-2 text-sm hover:bg-gray-100 {{ isset($currentCar) && $currentCar->id === $navCar->id ? 'font-semibold text-indigo-700' : 'text-gray-700' }}">
                                        {{ $navCar->name }}
                                        <span class="block text-xs text-gray-400">{{ $navCar->description() }}</span>
                                    </button>
                                </form>
                            @endforeach
                            <div class="border-t border-gray-100"></div>
                            <x-dropdown-link :href="route('cars.create')">+ Agregar carro</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menú móvil --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('records.index')" :active="request()->routeIs('records.*')">Registros</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('maintenance-types.index')" :active="request()->routeIs('maintenance-types.*')">Catálogo</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cars.index')" :active="request()->routeIs('cars.index', 'cars.create', 'cars.edit')">Mis carros</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cars.select')" :active="request()->routeIs('cars.select')">
                Cambiar de carro{{ isset($currentCar) ? ' (actual: '.$currentCar->name.')' : '' }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
