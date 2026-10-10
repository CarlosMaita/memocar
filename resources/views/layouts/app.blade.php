<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#000000">

        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'MemoCar') }}</title>

        @include('partials.assets')
    </head>
    <body class="font-sans antialiased bg-bg text-fg text-[15px] leading-normal">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (Auth::user()->is_demo)
                <div class="bg-soon-bg border-b border-soon-line">
                    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 text-sm">
                        <span class="text-soon-soft"><span class="font-bold text-soon">Modo demo.</span> Explora y edita libremente: estos datos son de prueba y se borran en 24 horas.</span>
                        <form method="POST" action="{{ route('demo.exit') }}">
                            @csrf
                            <button class="font-semibold text-soon underline underline-offset-2 hover:text-white">Crear mi cuenta gratis</button>
                        </form>
                    </div>
                </div>
            @endif

            <main class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-8 pb-16">
                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-ok-line bg-ok-bg px-4 py-3 text-sm text-ok-soft">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-over-line bg-over-bg px-4 py-3 text-sm text-over-soft">{{ session('error') }}</div>
                @endif

                @isset($header)
                    <div class="mb-6">{{ $header }}</div>
                @endisset

                {{ $slot }}
            </main>
        </div>
        <x-install-help />
    </body>
</html>
