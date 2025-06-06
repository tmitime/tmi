<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('projects.show', $project) }}">{{ __(':project', ['project' => $project->name]) }}</a> / {{ __('Edit project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <x-section-title>
                    <x-slot name="title">{{ __('Project details') }}</x-slot>
                    <x-slot name="description">
                        {{ __('Specify the name and the starting date to create a project.') }}<br/>
                        {{ __('You can also indicate the expected working days if planned.') }}
                    </x-slot>
                </x-section-title>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('projects.update', $project) }}" method="post">
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="">
                                @method('PUT')
                                @csrf

                                @include('projects.partials.details-form')
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                            <x-button class="ml-4">
                                {{ __('Update project') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>

            @livewire('project-member-manager', ['project' => $project])

            @if (Gate::check('delete', $project->team))
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('delete-project-form', ['project' => $project])
                </div>
            @endif

                    

        </div>
    </div>
</x-app-layout>
