<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span class="text-gray-500">{{ $team->name }} /</span> {{ __('Projects') }}
        </h2>

        @can('create', \App\Models\Project::class)
            <x-button-link href="{{ route('projects.create') }}" >
                {{ __('Create new project') }}
            </x-button-link>
        @endcan
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class="space-y-2">

                <div class="grid grid-cols-7">
                    <div class="col-span-3 text-sm uppercase text-gray-600">name</div>
                    <div class="col-span-2 text-sm uppercase text-gray-600">period</div>
                    <div class="text-sm uppercase text-gray-600">team</div>
                    <div class="text-sm uppercase text-gray-600">members</div>
                </div>
            
                @forelse ($projects as $project)

                    <a href="{{ route('projects.show', $project) }}" class="grid grid-cols-7 items-center py-2 border-b border-gray-200 hover:bg-white focus:outline-none focus:bg-white focus:ring focus:ring-indigo-500">
                        <div class="col-span-3">
                            <span class="text-indigo-600 text-lg" >{{ $project->name }}</span>
                            @if ($project->is_ongoing)
                                <x-badge class="ml-2 bg-yellow-100 text-yellow-700">{{ __('ongoing') }}</x-badge>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <x-time :time="$project->start_at" /> &mdash; <x-time :time="$project->end_at" default="{{ __('present') }}" />
                        </div>
                        <div>
                            <div class="truncate">{{ $project->team->name }}</div>
                        </div>
                        <div class="flex space-x-1">
                            @foreach ($project->allMembers() as $member)
                                <x-user-avatar width="w-6" height="h-6" :user="$member" />
                            @endforeach
                        </div>
                    </a>
                    

                @empty
                    
                    @include('projects.partials.empty-list')

                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
