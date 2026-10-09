<x-app-layout>
    <x-slot name="header">
        <span class="eyebrow text-accent-fg">Cuenta</span>
        <h1 class="mt-0.5 mb-0 font-display font-extrabold text-3xl sm:text-[42px] leading-tight tracking-tight text-white">Perfil</h1>
    </x-slot>

    <div>
        <div class="space-y-6">
            <div class="card p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
