<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('tasks.import.store') }}" method="post">
            
                @csrf


                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <x-section-title>
                        <x-slot name="title">{{ __('Project') }}</x-slot>
                        <x-slot name="description">
                            {{ __('Select a project to attach the imported tasks.') }}<br/>
                        </x-slot>
                    </x-section-title>
                
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="">
                            <livewire:project-input :selectedProject="old('project', optional($project)->uuid)" />
                            <x-input-error for="project" class="mt-2" />
                        </div>
                    </div>
                </div>


                <div class="mt-8 md:grid md:grid-cols-3 md:gap-6">
                    <x-section-title>
                        <x-slot name="title">{{ __('Tasks') }}</x-slot>
                        <x-slot name="description">
                            {{ __('Specify each task to be imported in its own line.') }}<br/>
                            {{ __('The format for each line must be:') }}<br/>
                            <code>YYYY-MM-DD;m|h;duration;description</code><br>(<code>m</code> = {{ __('minutes') }}, <code>h</code> = {{ __('hours') }})<br/>

                        </x-slot>
                    </x-section-title>

                    <div class="mt-5 md:mt-0 md:col-span-2">
        
                        <x-label for="tasks" value="{{ __('Tasks to import') }}" />
                        <textarea id="tasks" name="tasks" class="w-full h-64 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('tasks') }}</textarea>
                        @error('tasks')
                            <div class="text-sm text-red-600 mt-2 space-y-4">

                                @php
                                    $messages = json_decode($message, true);
                                @endphp

                                @foreach ($messages as $line => $errors)
                                    <div class="mb-4">

                                        <p class="mb-2">{{ $line }}</p>

                                        <ul class="pl-4 list-disc">
                                            @foreach ($errors as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach

                                
                            </div>
                        @enderror

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Import') }}
                            </x-button>
                        </div>

                    </div>
                </div>
    
</form>

                    

        </div>
    </div>
</x-app-layout>
