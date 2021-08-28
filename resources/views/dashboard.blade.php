<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <ul class="list-disc">
                <li>Option to create a new tracking timer</li>
                <li>Option to quickly add a tracked time to a project</li>
                <li>Show how's going your day</li>
                <li></li>
            </ul>
        </div>
    </div>
</x-app-layout>
