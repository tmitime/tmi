<x-action-section>
    <x-slot name="title">
        {{ __('Delete Project') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete this project.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Once a project is deleted, all of its resources and data will be permanently deleted. Before deleting this project, please download any data or information regarding this project that you wish to retain.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="$toggle('confirmingProjectDeletion')" wire:loading.attr="disabled">
                {{ __('Delete Project') }}
            </x-danger-button>
        </div>

        <!-- Delete Project Confirmation Modal -->
        <x-confirmation-modal wire:model.live="confirmingProjectDeletion">
            <x-slot name="title">
                {{ __('Delete Project') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to delete ":project"? Once a project is deleted, all of its resources and data will be permanently deleted.', ['project' => $this->project->name]) }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingProjectDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-2" wire:click="deleteProject" wire:loading.attr="disabled">
                    {{ __('Delete Project') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </x-slot>
</x-action-section>
