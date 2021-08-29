<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if ($project)
            <a href="{{ route('projects.show', $project) }}">{{ __(':project', ['project' => $project->name]) }}</a> /    
            @endif
             <a href="{{ route('tasks.index', ['project' => $project]) }}">{{ __('Tasks') }}</a> / {{ __('Edit task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <x-jet-section-title>
                    <x-slot name="title">{{ __('Task details') }}</x-slot>
                    <x-slot name="description">
                        {{ __('Describe the performed activity and the time it required.') }}<br/>
                        {{ __('Optionally apply a type to easy the grouping') }}
                    </x-slot>
                </x-jet-section-title>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('tasks.update', $task) }}" method="post">
                        
                        @method('PUT')
                        @csrf

                        @include('tasks.partials.details-form')

                        <div class="flex items-center justify-end mt-4">

                            <x-jet-button class="ml-4">
                                {{ __('Save task') }}
                            </x-jet-button>
                        </div>

                    </form>
                </div>
            </div> 

            <x-jet-section-border />

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <x-jet-section-title>
                    <x-slot name="title">{{ __('Task history') }}</x-slot>
                    <x-slot name="description">
                        {{ __('History of changes related to the tracked activity.') }}
                    </x-slot>
                </x-jet-section-title>

                <div class="mt-5 md:mt-0 md:col-span-2">

                    @if ($task->is_edited)
                        <p class="mb-4">{{ $task->updated_at }} {{ __('Task edited') }}</p>
                        
                    @endif

                    <p>{{ $task->created_at }} {{ __('Task reported for the first time') }}</p>
                </div>
            </div> 
            <x-jet-section-border />

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <x-jet-section-title>
                    <x-slot name="title">{{ __('Delete task') }}</x-slot>
                    <x-slot name="description">
                        {{ __('Permanently delete this task.') }}
                    </x-slot>
                </x-jet-section-title>

                <div class="mt-5 md:mt-0 md:col-span-2">

                    <form action="{{ route('tasks.destroy', $task) }}" method="post">
                        @method('DELETE')
                        @csrf

                        <x-jet-danger-button type="submit">
                            {{ __('Delete task') }}
                        </x-jet-danger-button>
                    </form>


                </div>
            </div> 

        </div>
    </div>
</x-app-layout>
