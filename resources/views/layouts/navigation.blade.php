@php($navCars = Auth::user()->cars()->orderBy('name')->get(['id', 'name', 'brand', 'model', 'year']))
@php($tabs = [
    ['Dashboard', 'dashboard', 'dashboard'],
    ['Registros', 'records.index', 'records.*'],
    ['Catálogo', 'maintenance-types.index', 'maintenance-types.*'],
    ['Mis carros', 'cars.index', ['cars.index', 'cars.create', 'cars.edit']],
    ...(Auth::user()->is_admin ? [['Admin', 'admin', 'admin']] : []),
])
<nav x-data="{ open: false }" class="bg-bar border-b border-[#222622]">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 min-h-16 flex items-center gap-x-8 gap-y-3">
        <a href="{{ route('dashboard') }}" class="no-underline shrink-0"><x-brand /></a>

        <div class="hidden md:flex gap-1 flex-1">
            @foreach ($tabs as [$label, $route, $pattern])
                @php($active = request()->routeIs(...(array) $pattern))
                <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
                   @class([
                       'px-3.5 py-2 rounded-md text-sm no-underline',
                       'bg-surface-2 text-white font-semibold shadow-[inset_0_-2px_0_var(--color-accent)]' => $active,
                       'text-muted font-medium hover:text-white' => ! $active,
                   ])>{{ $label }}</a>
            @endforeach
        </div>

        <div class="hidden md:flex items-center gap-3 ms-auto">
            @if ($navCars->isNotEmpty())
                <x-dropdown align="right" width="w-64" contentClasses="py-1 bg-surface border border-line">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center gap-2.5 py-1.5 ps-1.5 pe-3 rounded-full bg-surface border border-line-2 text-white">
                            <span class="w-[30px] h-[30px] rounded-full bg-accent text-on-accent flex items-center justify-center font-extrabold text-[13px]">{{ mb_strtoupper(mb_substr($currentCar->name ?? '?', 0, 1)) }}</span>
                            <span class="text-sm font-semibold">{{ $currentCar->name ?? 'Elegir carro' }}</span>
                            <svg aria-hidden="true" class="w-4 h-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10l5 5 5-5"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 eyebrow text-[11px] text-muted">Cambiar de carro</div>
                        @foreach ($navCars as $navCar)
                            <form method="POST" action="{{ route('cars.choose', $navCar) }}">
                                @csrf
                                <button @class([
                                    'w-full text-start px-4 py-2 text-sm hover:bg-surface-2',
                                    'font-semibold text-accent-fg' => isset($currentCar) && $currentCar->id === $navCar->id,
                                    'text-fg' => ! (isset($currentCar) && $currentCar->id === $navCar->id),
                                ])>
                                    {{ $navCar->name }}
                                    <span class="block text-xs text-muted font-normal">{{ $navCar->description() }}</span>
                                </button>
                            </form>
                        @endforeach
                        <div class="border-t border-line my-1"></div>
                        <x-dropdown-link :href="route('cars.create')">+ Agregar carro</x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            @endif

            <x-dropdown align="right" width="48" contentClasses="py-1 bg-surface border border-line">
                <x-slot name="trigger">
                    <button type="button" aria-label="Cuenta de {{ Auth::user()->name }}" class="w-10 h-10 rounded-full border border-line-2 text-white font-semibold text-sm">
                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <div class="px-4 py-2 border-b border-line">
                        <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-muted truncate">{{ Auth::user()->is_demo ? 'Cuenta demo' : Auth::user()->email }}</div>
                    </div>
                    @unless (Auth::user()->is_demo)
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                    @endunless
                    <x-install-app />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <button type="button" @click="open = ! open" :aria-expanded="open" aria-label="Abrir menú" class="md:hidden ms-auto w-11 h-11 rounded-lg border border-line-2 flex items-center justify-center text-white">
            <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path x-show="! open" d="M4 7h16M4 12h16M4 17h16" />
                <path x-show="open" x-cloak d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Menú móvil --}}
    <div x-show="open" x-cloak class="md:hidden border-t border-[#222622] px-4 pb-4">
        <div class="pt-3 flex flex-col gap-1">
            @foreach ($tabs as [$label, $route, $pattern])
                <x-responsive-nav-link :href="route($route)" :active="request()->routeIs(...(array) $pattern)">{{ $label }}</x-responsive-nav-link>
            @endforeach
            <x-responsive-nav-link :href="route('cars.select')" :active="request()->routeIs('cars.select')">
                Cambiar de carro{{ isset($currentCar) ? ' · '.$currentCar->name : '' }}
            </x-responsive-nav-link>
        </div>
        <div class="mt-3 pt-3 border-t border-line flex flex-col gap-1">
            <div class="px-3 pb-1">
                <div class="font-semibold text-white">{{ Auth::user()->name }}</div>
                <div class="text-sm text-muted">{{ Auth::user()->is_demo ? 'Cuenta demo' : Auth::user()->email }}</div>
            </div>
            @unless (Auth::user()->is_demo)
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
            @endunless
            <x-install-app />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
