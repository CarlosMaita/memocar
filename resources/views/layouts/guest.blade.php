<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#000000">

        <title>{{ config('app.name', 'MemoCar') }}</title>

        @include('partials.assets')
    </head>
    <body class="font-sans antialiased bg-bg text-fg text-[15px] leading-normal">
        <div class="min-h-screen flex flex-wrap">
            {{-- Panel de marca --}}
            <section class="flex-[1_1_480px] min-w-0 bg-bar border-b lg:border-b-0 lg:border-r border-[#222622] p-8 sm:p-12 flex flex-col justify-between gap-10">
                <a href="/" class="no-underline"><x-brand size="lg" /></a>

                <div class="flex flex-col gap-5 max-w-[460px]">
                    <h1 class="m-0 font-display font-extrabold text-4xl sm:text-5xl leading-[1.04] tracking-tight text-white">
                        La memoria de tu carro, <span class="text-accent-fg">siempre al día.</span>
                    </h1>
                    <p class="m-0 text-[17px] text-muted">Anota cada reparación y mantenimiento. MemoCar calcula cuándo toca el próximo por kilometraje y por fecha, lo que ocurra primero.</p>

                    <div aria-hidden="true" class="hidden sm:flex mt-3 card p-4 flex-col gap-2.5 max-w-[380px]">
                        <div class="flex justify-between items-center gap-3">
                            <span class="font-semibold text-white">Cambio de aceite y filtro</span>
                            <span class="pill bg-soon-bg text-soon">Próximo</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-line overflow-hidden"><div class="h-full w-[88%] rounded-full bg-soon-bar"></div></div>
                        <span class="text-[13px] text-muted">Faltan 600 km · cada 5.000 km / 6 meses</span>
                    </div>
                </div>

                <span class="hidden sm:block text-[13px] text-[#7E857A]">memocar.upsofte.com</span>
            </section>

            {{-- Formulario --}}
            <section class="flex-[1_1_420px] min-w-0 px-6 py-12 flex items-center justify-center">
                <div class="w-full max-w-[380px] flex flex-col gap-8">
                    <div>{{ $slot }}</div>
                    <x-install-app variant="card" />
                </div>
            </section>
        </div>
        <x-install-help />
    </body>
</html>
