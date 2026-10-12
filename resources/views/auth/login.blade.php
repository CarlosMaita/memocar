<x-guest-layout>
    @php($codes = config('auth.login_codes'))
    @php($usePassword = ! $codes || old('mode') === 'password' || $errors->has('password'))
    <div class="flex flex-col gap-[18px]" x-data="{ mode: '{{ $usePassword ? 'password' : 'code' }}', toggled: false }">
        <div>
            <h2 class="m-0 font-display font-extrabold text-3xl text-white">Iniciar sesión</h2>
            <p class="mt-1.5 mb-0 text-muted">¿No tienes cuenta? <a href="{{ route('register') }}" class="font-semibold">Regístrate gratis</a></p>
        </div>

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <div role="alert" class="px-3.5 py-2.5 rounded-lg bg-over-bg border border-over-line text-over-soft text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" :action="mode === 'code' ? '{{ $codes ? route('login.code.send') : '' }}' : '{{ route('login') }}'" action="{{ $codes ? route('login.code.send') : route('login') }}" class="flex flex-col gap-[18px]">
            @csrf
            <input type="hidden" name="mode" :value="mode">

            <div class="flex flex-col gap-1.5">
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" inputmode="email" />
            </div>

            @if ($codes) <template x-if="mode === 'password'"> @endif
                <div class="flex flex-col gap-[18px]">
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-baseline">
                            <x-input-label for="password" value="Contraseña" />
                            <a href="{{ route('password.request') }}" class="text-sm font-medium">¿La olvidaste?</a>
                        </div>
                        <x-text-input id="password" class="w-full" type="password" name="password" required autocomplete="current-password" x-init="toggled && $el.focus()" />
                    </div>
                    <label for="remember_me" class="flex items-center gap-2.5 text-sm text-fg-2 min-h-8">
                        <input id="remember_me" type="checkbox" name="remember">
                        Mantener sesión activa
                    </label>
                </div>
            @if ($codes) </template> @endif

            <x-primary-button class="w-full min-h-12 text-base">
                @if ($codes)
                    <span x-show="mode === 'code'">Enviarme un código</span>
                    <span x-show="mode === 'password'" x-cloak>Entrar</span>
                @else
                    Entrar
                @endif
            </x-primary-button>

            @if ($codes)
                <p x-show="mode === 'code'" class="m-0 -mt-2 text-[13px] text-muted text-center">Te llega un código de 6 dígitos a tu correo. Sin contraseñas que recordar.</p>
            @endif

            @if ($codes)
            <button type="button" class="text-sm font-semibold text-accent-fg hover:text-white"
                    @click="toggled = true; mode = mode === 'code' ? 'password' : 'code'"
                    x-text="mode === 'code' ? 'Prefiero entrar con contraseña' : 'Entrar con un código por correo'">Prefiero entrar con contraseña</button>
            @endif
        </form>

        <x-demo-button />
    </div>
</x-guest-layout>
