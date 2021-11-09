<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}

            @if ($hasFilters)
                <span class="font-normal text-gray-600">
                    {
                    @foreach ($filters as $filter => $value)
                        @if ($value instanceof \App\Models\Project)
                        {{ $filter }} = " <a href="{{ route('projects.show', $value) }}" class="underline">{{ $value->name ?? 'all' }}</a> "
                        @else
                            {{ $filter }} = " {{ $value->name ?? 'all' }} "
                        @endif
                    @endforeach
                    }
                </span>
            @endif
        </h2>

        @can('create', [\App\Models\Task::class, $filters['project'] ?? null])
            @php
                $prj = optional($filters['project'] ?? null)->uuid
            @endphp
            <div class="space-x-2">
                <x-button-link href="{{ route('tasks.create', $prj ? ['project' => $prj] : []) }}" >
                    {{ __('Track a task') }}
                </x-button-link>
                <x-button-link href="{{ route('tasks.import.create', $prj ? ['project' => $prj] : []) }}" >
                    {{ __('Import tasks') }}
                </x-button-link>
            </div>
        @endcan
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class="">

                <div class="grid grid-cols-5">
                    <div class="text-sm uppercase text-gray-600">duration</div>
                    <div class="col-span-2 text-sm uppercase text-gray-600">description</div>
                    <div class="text-sm uppercase text-gray-600">project</div>
                    <div class="text-sm uppercase text-gray-600">date</div>
                </div>
            
                @forelse ($tasks as $task)

                <div class="grid grid-cols-5 py-2 border-b border-gray-200 hover:bg-white">
                    <div>{{ $task->duration }} {{ __('minutes') }}</div>
                    <div class="col-span-2">
                        <a href="{{ route('tasks.edit', $task) }}" class="underline">
                            {{ $task->description }}
                            @if ($task->is_meeting)
                                <x-badge class="bg-blue-100 text-blue-700">{{ __('meeting') }}</x-badge>
                            @endif
                        </a>
                    </div>
                    <div>@if ($task->project)
                        <a class="underline" href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a>
                    @endif</div>
                    <div><x-time :time="$task->created_at" /> {!! $task->is_edited ? '<abbr title="Updated after creation">*</abbr>':'' !!}</div>
                </div>
                    

                @empty
                    
                    <div class="col-span-5 p-8">
        
                        <p class="font-bold">
                            {{ __('No tasks reported on your side') }}
                        </p>

                        @can('create', \App\Models\Task::class)
                            <p class="text-gray-600">{{ __('Track an acttivity') }}</p>
                            
                            <x-button-link href="{{ route('tasks.create') }}" >
                                {{ __('Track activity') }}
                            </x-button-link>
                        @endcan
                    </div>



                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
