<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span class="text-gray-500">{{ $team->name }} /</span> {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-3 gap-6">
                @forelse ($projects as $project)
                    <x-project-card :project="$project" />
                @empty
                    @include('projects.partials.empty-list')
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
