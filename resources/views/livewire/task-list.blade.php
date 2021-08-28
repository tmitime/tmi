<div class="">

    <div class="grid grid-cols-5">
        <div class="text-sm uppercase text-gray-600">duration</div>
        <div class="col-span-2 text-sm uppercase text-gray-600">description</div>
        <div class="text-sm uppercase text-gray-600">user</div>
        <div class="text-sm uppercase text-gray-600">date</div>
    </div>

    @forelse ($tasks as $task)
        <a href="{{ route('tasks.edit', $task) }}" class="grid grid-cols-5 py-2 border-b border-gray-200 hover:bg-white focus:outline-none focus:bg-white focus:ring focus:ring-indigo-500">
            <div>{{ $task->duration }} {{ __('minutes') }}</div>
            <div class="col-span-2">
                {{ $task->description }}
                @if ($task->is_meeting)
                    <x-badge class="bg-blue-100 text-blue-700">{{ __('meeting') }}</x-badge>
                @endif
            </div>
            <div>{{ $task->user->name }}</div>
            <div><x-time :time="$task->created_at" /> {!! $task->is_edited ? '<abbr title="Updated after creation">*</abbr>':'' !!}</div>
        </a>
    @empty
        <div class="col-span-5 text-gray-600">
            {{ __('No tasks recorded.') }}
        </div>
    @endforelse
</div>
