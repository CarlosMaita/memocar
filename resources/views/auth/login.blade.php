<x-guest-layout>
    <div class="flex flex-col gap-[18px]">
        <div>
            <h2 class="m-0 font-display font-extrabold text-3xl text-white">Iniciar sesión</h2>
            <p class="mt-1.5 mb-0 text-muted">¿No tienes cuenta? <a href="{{ route('register') }}" class="font-semibold">Regístrate gratis</a></p>
        </div>

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <div role="alert" class="px-3.5 py-2.5 rounded-lg bg-over-bg border border-over-line text-over-soft text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-[18px]">
            @csrf

            <div class="flex flex-col gap-1.5">
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="flex justify-between items-baseline">
                    <x-input-label for="password" value="Contraseña" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium">¿La olvidaste?</a>
                    @endif
                </div>
                <x-text-input id="password" class="w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <label for="remember_me" class="flex items-center gap-2.5 text-sm text-fg-2 min-h-8">
                <input id="remember_me" type="checkbox" name="remember">
                Mantener sesión activa
            </label>

            <x-primary-button class="w-full min-h-12 text-base">Entrar</x-primary-button>
        </form>

        <x-demo-button />
    </div>
</x-guest-layout>
