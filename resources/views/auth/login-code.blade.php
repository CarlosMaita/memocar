<x-guest-layout>
    <div class="flex flex-col gap-[18px]">
        <div>
            <h2 class="m-0 font-display font-extrabold text-3xl text-white">Revisa tu correo</h2>
            <p class="mt-1.5 mb-0 text-muted">Si hay una cuenta con <span class="text-white font-semibold">{{ $maskedEmail }}</span>, te enviamos un código de 6 dígitos. Vence en {{ $minutes }} minutos.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <div role="alert" class="px-3.5 py-2.5 rounded-lg bg-over-bg border border-over-line text-over-soft text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.code.verify') }}" class="flex flex-col gap-[18px]" x-data="{ code: '' }">
            @csrf
            <div class="flex flex-col gap-1.5">
                <x-input-label for="code" value="Código" />
                <input id="code" name="code" x-model="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]*" maxlength="7" required autofocus
                       placeholder="000000" aria-describedby="code-help"
                       @input="code = code.replace(/\D/g, '').slice(0, 6); if (code.length === 6) $nextTick(() => $el.form.requestSubmit())"
                       class="w-full min-h-16 text-center font-mono font-semibold text-3xl tracking-[0.5em] placeholder:text-line-2">
                <span id="code-help" class="text-[13px] text-muted">También puedes tocar <span class="text-fg-2 font-semibold">“Entrar a MemoCar”</span> en el correo.</span>
            </div>
            <x-primary-button class="w-full min-h-12 text-base">Entrar</x-primary-button>
        </form>

        <div class="flex flex-wrap items-center justify-between gap-3 text-sm" x-data="{ wait: {{ $resendIn }} }" x-init="setInterval(() => wait > 0 && wait--, 1000)">
            <a href="{{ route('login') }}" class="text-muted hover:text-white">Usar otro correo</a>
            <form method="POST" action="{{ route('login.code.resend') }}">
                @csrf
                <button class="font-semibold text-accent-fg hover:text-white disabled:text-muted disabled:cursor-not-allowed" :disabled="wait > 0">
                    <span x-show="wait > 0" x-cloak>Reenviar en <span x-text="wait"></span> s</span>
                    <span x-show="wait === 0">Reenviar código</span>
                </button>
            </form>
        </div>

        <p class="m-0 text-[13px] text-muted">¿No llega? Revisa la carpeta de spam o promociones.</p>
    </div>
</x-guest-layout>
