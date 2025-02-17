<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span class="text-gray-500">{{ $project->team->name }} /</span> {{ $project->name }}
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
                @can('create', [\App\Models\Task::class, $project])
                    <div class="bg-gray-50 rounded-md shadow-lg p-4">
                        <h3 class="font-bold mb-3">{{ __('Track time') }}</h3>

                        <livewire:track-activity :project="$project" />
                    </div>
                @endcan
                
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
                        <p><x-time :time="$project->start_at" /> &mdash; <x-time :time="$project->end_at" default="{{ __('ongoing') }}" /></p>
                        
                        @if ($project->working_days)
                            <p>{{ $project->working_days }} {{ __('working days') }}</p>
                        @endif
                    </div>

                    @if ($project->description)
                        <x-markdown class="mt-2" :value="$project->description" />
                    @endif

                </div>
                <div class="pb-6 md:border-b border-gray-300">
                    <h3 class="font-bold mb-3">{{ __('Status') }}</h3>

                    <livewire:project-tasks-stats-meter :project="$project" />

                </div>
                <div class="pb-6 md:border-b border-gray-300">
                    <h3 class="font-bold mb-3">{{ __('Members') }}</h3>
                    <div class="flex items-center space-x-2">
                        @foreach ($project->allMembers() as $member)

                            <x-user-avatar width="w-10" height="h-10" :user="$member" />

                        @endforeach
                    </div>
                </div>
                <div>

                    <h3 class="font-bold mb-3">{{ __('Actions') }}</h3>

                    <ul class="mb-4 flex flex-col space-y-2">
                        <li>
                            <a class="underline" href="{{ route('tasks.index', ['project' => $project]) }}">{{ __('View all tasks') }}</a>
                        </li>
                        <li></li>
                        <li>
                            <a class="underline" target="_blank" href="{{ route('projects.report', ['project' => $project]) }}">{{ __('Current month report') }}</a>
                        </li>
                        <li>
                            <a class="underline" target="_blank" href="{{ route('projects.report', ['project' => $project, 'period' => \App\Enum\ReportingPeriod::PREVIOUS_MONTH->value]) }}">{{ __('Previous month report') }}</a>
                        </li>
                        <li>
                            <a class="underline" target="_blank" href="{{ route('projects.report', ['project' => $project, 'period' => \App\Enum\ReportingPeriod::OVERALL->value]) }}">{{ __('Full project report') }}</a>
                        </li>
                        <li></li>
                        @can('create', [\App\Models\Task::class, $project])
                            <li>
                                <a class="underline" href="{{ route('tasks.import.create', ['project' => $project]) }}">{{ __('Import tasks') }}</a>
                            </li>
                            <li>
                                <a class="underline" target="_blank" href="{{ route('tasks.export.show', ['project' => $project]) }}">{{ __('Export tasks') }}</a>
                            </li>
                        @endcan
                    </ul>

                    <x-label>{{ __('Reference') }}</x-label>
                    <code class="w-full whitespace-nowrap">{{ $project->uuid }}</code>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
