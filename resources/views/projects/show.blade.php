<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->name }}
        </h2>

        @can('update', $project)
            <x-button-link href="{{ route('projects.edit', $project) }}" >
                {{ __('Edit project') }}
            </x-button-link>
        @endcan
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col-reverse md:flex-row space-x-6">
            <div class="flex-grow space-y-6">
                
                <div class="bg-gray-50 rounded-md shadow-lg p-4">
                    <h3 class="font-bold mb-3">{{ __('Track time') }}</h3>

                    <livewire:track-activity :project="$project" />
                </div>
                
                <livewire:project-summary :project="$project" />
                
                <div class=" p-4">
                    <h3 class="font-bold mb-3">{{ __('Latest tracked activities') }}</h3>

                    <livewire:task-list :project="$project" />

                    @if ($project->latestTasks->isNotEmpty())
                        <a class="mt-2 inline-block underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('tasks.index', ['project' => $project]) }}">
                            {{ __('View all tasks') }}
                        </a>
                    @endif
                </div>

                


            </div>
            
            <div class="flex space-x-8 md:flex-col md:space-x-0 md:space-y-6 md:w-1/4">
                <div class="pb-6 md:border-b border-gray-300">
                    <h3 class="font-bold mb-3">{{ __('About') }}</h3>

                    <div class="space-y-2">
                        <p class="">{{ $project->name }}</p>
                        <p><x-time :time="$project->start_at" /> &mdash; <x-time :time="$project->end_at" default="{{ __('ongoing') }}" /></p>
                        
                        @if ($project->working_days)
                            <p>{{ $project->working_days }} {{ __('working days') }}</p>
                        @endif
                    </div>
                </div>
                <div class="pb-6 md:border-b border-gray-300">
                    <h3 class="font-bold mb-3">{{ __('Status') }}</h3>

                    <livewire:project-tasks-stats-meter :project="$project" />

                </div>
                <div class="pb-6 md:border-b border-gray-300">
                    <h3 class="font-bold mb-3">{{ __('Members') }}</h3>
                    <div class="flex justify-between items-center">
                        @foreach ($project->members as $member)

                            <x-user-avatar width="w-10" height="h-10" :user="$member" />

                        @endforeach
                    </div>
                </div>
                <div>
                    <x-jet-label>{{ __('Reference') }}</x-jet-label>
                    <code class="w-full whitespace-nowrap overflow-ellipsis">{{ $project->uuid }}</code>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
