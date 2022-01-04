<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h3 class="font-semibold text-xl text-gray-600 leading-tight">
                {{ $team->name }}
            </h3>
            <p class="mb-4 text-gray-600">{{ __('Projects in the currently selected team') }}</p>

            <div class="grid grid-cols-3 gap-6">
                @forelse ($projects as $project)
                    <x-project-card :project="$project" />
                @empty
                    @include('projects.partials.empty-list')
                @endforelse
            </div>

        </div>
    </div>
    
    @if ($shared->isNotEmpty())
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <h3 class="font-semibold text-xl text-gray-600 leading-tight">
                    {{ __('Shared projects') }}
                </h3>
                <p class="mb-4 text-gray-600">{{ __('Projects you are member that are not part of the current selected team') }}</p>
                
                <div class="grid grid-cols-3 gap-6">
                    @forelse ($shared as $project)
                    <x-project-card :project="$project" />
                    @empty
                    @include('projects.partials.empty-list')
                    @endforelse
                </div>
                
            </div>
        </div>
    @endif
</x-app-layout>
