<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MemoCar') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @include('partials.assets')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">
            <a href="/" class="flex flex-col items-center gap-1">
                <x-application-logo class="w-16 h-16 text-indigo-600" />
                <span class="text-2xl font-semibold tracking-tight">MemoCar</span>
                <span class="text-sm text-gray-500">El historial de mantenimiento de tus carros</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-md overflow-hidden rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
