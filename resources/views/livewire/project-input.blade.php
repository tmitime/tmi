<div class="relative">
    <x-label for="project" class="mb-2">{{ __('Project') }}</x-label>

    <div class="relative">
        <input
            type="text"
            class="relative w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            placeholder="{{ __('Type to search for project...') }}"
            wire:focus.prefetch="fetchAutocomplete"
            wire:model="query"
            wire:keydown.escape="hideDropdown"
            wire:keydown.tab="hideDropdown"
            wire:blur="hideDropdown"
            wire:keydown.Arrow-Up="decrementHighlight"
            wire:keydown.Arrow-Down="incrementHighlight"
            wire:keydown.enter.prevent="selectProject"
        />

        <input type="hidden" name="project" id="project" wire:model="selectedProject">

        @if ($selectedProject)
            <button type="button" title="{{ __('Clear') }}" class="absolute cursor-pointer top-2 right-2 text-gray-500" wire:click="reset">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        @endif
    </div>

    @if($showDropdown)
        <div class="absolute z-10 mt-1 py-2 bg-white bg-opacity-40 backdrop-blur-sm w-full border border-gray-300 rounded-md shadow-lg overflow-hidden">
            @if (!empty($projects))
                @foreach($projects as $i => $project)
                    <a
                        wire:click="selectProject({{ $i }})"
                        class="block py-1 px-2 cursor-pointer hover:bg-blue-50 hover:bg-opacity-75 {{ ($highlightIndex === $i || $selectedProject === $project['uuid']) ? 'font-bold ring ring-blue-200 ring-opacity-50 border-indigo-300' : '' }}"
                    >{{ $project['name'] }}</a>
                @endforeach
            @else
                <span class="block py-1 px-2 text-gray-600">{{ __('No project found!') }}</span>
            @endif
        </div>
    @endif
</div>