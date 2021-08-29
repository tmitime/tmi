@props(['project'])

<div {{ $attributes->merge(['class' => 'h-40']) }}>
    <a href="{{ route('projects.show', $project) }}" class=" bg-white rounded-md shadow-md border border-gray-200 space-y-2 h-full flex flex-col justify-between overflow-hidden p-4 hover:bg-white hover:border-indigo-500 focus:border-indigo-500  focus:outline-none focus:bg-white focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        <div class="flex items-center space-x-2">
            @if ($project->is_ongoing)
                <x-badge class="bg-yellow-100 text-yellow-700">{{ __('ongoing') }}</x-badge>
            @endif
        </div>
        <div class="flex-grow flex items-start">
            <span class="font-bold text-xl" >{{ $project->name }}</span>
        </div>
        <div class="flex items-center justify-between text-base">
            <div>
                <x-time :time="$project->start_at" /> &mdash; <x-time :time="$project->end_at" default="{{ __('present') }}" />
            </div>
            <div>
                @foreach ($project->members as $member)
                    <x-user-avatar width="w-6" height="h-6" :user="$member" />
                @endforeach
            </div>
        </div>
    </a>
</div>