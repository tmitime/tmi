<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report a performed task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<div class="md:grid md:grid-cols-3 md:gap-6">
    <x-section-title>
        <x-slot name="title">{{ __('Task details') }}</x-slot>
        <x-slot name="description">
            {{ __('Describe the performed activity and the time it required.') }}<br/>
            {{ __('Optionally apply a type to easy the grouping') }}
        </x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form action="{{ route('tasks.store') }}" method="post">
            
            @csrf

            <div class="">
                <livewire:project-input :selectedProject="old('project', optional($project)->uuid)" />
                <x-input-error for="project" class="mt-2" />
            </div>

            @include('tasks.partials.details-form', ['create' => true])

            <div class="flex items-center justify-end mt-4">

                <x-button class="ml-4">
                    {{ __('Save task') }}
                </x-button>
            </div>

        </form>
    </div>
</div>


                    

        </div>
    </div>
</x-app-layout>
