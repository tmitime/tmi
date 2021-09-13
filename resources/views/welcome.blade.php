<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        {{ config('app.name') }}
    </x-jet-authentication-card>
</x-guest-layout>
