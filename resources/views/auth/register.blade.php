<x-guest-layout>
    <div class="flex flex-col gap-[18px]">
        <div>
            <h2 class="m-0 font-display font-extrabold text-3xl text-white">Crea tu cuenta</h2>
            <p class="mt-1.5 mb-0 text-muted">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-semibold">Inicia sesión</a></p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-[18px]">
            @csrf

            <div class="flex flex-col gap-1.5">
                <x-input-label for="name" value="Nombre" />
                <x-text-input id="name" class="w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="password" value="Contraseña" />
                <x-text-input id="password" class="w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" class="w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>

            <x-primary-button class="w-full min-h-12 text-base">Crear cuenta</x-primary-button>
        </form>
    </div>
</x-guest-layout>
