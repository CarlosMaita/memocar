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
    </body>
</html>
