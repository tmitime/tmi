<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @livewireStyles

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-favicon />
    </head>
    <body>
        <div class="font-sans text-gray-900">
            {{ $slot }}
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
