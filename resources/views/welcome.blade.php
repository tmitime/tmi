<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{ config('app.name') }}
    </x-authentication-card>
</x-guest-layout>
