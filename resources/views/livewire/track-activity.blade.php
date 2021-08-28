<div>

    <form action="{{ route('tasks.store') }}" wire:submit.prevent="saveEntry" method="post" class="grid grid-cols-3 gap-4">

        <div class="">
            <x-jet-label for="duration" value="{{ __('Duration (minutes)') }}" />
            <x-jet-input id="duration" class="block mt-1 w-full" type="number" min="1" name="duration" wire:model.defer="task.duration" required autofocus />
            <x-jet-input-error for="task.duration" class="mt-2" />
        </div>
    
        <div class="col-span-2">
            <x-jet-label for="description" value="{{ __('Activity') }}" />
            <x-jet-input id="description" class="block mt-1 w-full" type="text" name="description"  wire:model.defer="task.description" autofocus />
            <x-jet-input-error for="task.description" class="mt-2" />
        </div>
    
        <div class="col-span-3 flex space-x-2 items-center">
    
            <x-jet-button class="">
                {{ __('Add activity') }}
            </x-jet-button>

            <span wire:loading wire:target="saveEntry">
                Saving task...
            </span>

            @if (session()->has('flash.banner'))
            
                <span wire:loading.remove x-data="{ isVisible: @entangle('showSavedState') }"
                x-init="
                    setTimeout(() => {
                        isVisible = false
                    }, 2000)
                "
                x-show="isVisible" class="inline-flex text-green-600">
                    
                    <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    {{ session('flash.banner') }}
                </span>
            @endif
        </div>
    </form>
</div>
