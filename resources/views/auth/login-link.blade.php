<x-guest-layout>
    <div class="flex flex-col gap-[18px]">
        <div>
            <h2 class="m-0 font-display font-extrabold text-3xl text-white">Entrar a MemoCar</h2>
            <p class="mt-1.5 mb-0 text-muted">Confirma para iniciar sesión en este dispositivo.</p>
        </div>
        <form method="POST" action="{{ route('login.link.use', $token) }}">
            @csrf
            <x-primary-button class="w-full min-h-12 text-base" autofocus>Entrar</x-primary-button>
        </form>
        <a href="{{ route('login') }}" class="text-sm text-muted hover:text-white">Cancelar</a>
    </div>
</x-guest-layout>
