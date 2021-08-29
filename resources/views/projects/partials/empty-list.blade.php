<div class="col-span-5 p-8 space-y-3 bg-white">
    <p class="font-bold">
        {{ __('No projects') }}
    </p>

    @can('create', \App\Models\Project::class)
        <p class="text-gray-600">{{ __('Get started by creating a new project') }}</p>
        
        <x-button-link href="{{ route('projects.create') }}" >
            {{ __('New project') }}
        </x-button-link>
    @else
        <p class="text-gray-600">{{ __('Hopefully you\'ll be invited to a project soon') }}</p>
    @endcan
</div>