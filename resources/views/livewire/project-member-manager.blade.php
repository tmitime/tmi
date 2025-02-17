<div>
    @if (Gate::check('addProjectMember', $project))
        <x-section-border />

        <!-- Add Project Member -->
        <div class="mt-10 sm:mt-0">
            <x-form-section submit="addProjectMember">
                <x-slot name="title">
                    {{ __('Add Project Member') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Add a new project member to your project, allowing them to collaborate with you.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            {{ __('Please provide the email address of the person you would like to add to this project.') }}
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" type="email" class="mt-1 block w-full" wire:model="addProjectMemberForm.email" />
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    @if (count($this->roles) > 0)
                        <div class="col-span-6 lg:col-span-4">
                            <x-label for="role" value="{{ __('Role') }}" />
                            <x-input-error for="role" class="mt-2" />

                            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                                @foreach ($this->roles as $index => $role)
                                    <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}"
                                                    wire:click="$set('addProjectMemberForm.role', '{{ $role->key }}')">
                                        <div class="{{ isset($addProjectMemberForm['role']) && $addProjectMemberForm['role'] !== $role->key ? 'opacity-50' : '' }}">
                                            <!-- Role Name -->
                                            <div class="flex items-center">
                                                <div class="text-sm text-gray-600 {{ $addProjectMemberForm['role'] == $role->key ? 'font-semibold' : '' }}">
                                                    {{ $role->name }}
                                                </div>

                                                @if ($addProjectMemberForm['role'] == $role->key)
                                                    <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>

                                            <!-- Role Description -->
                                            <div class="mt-2 text-xs text-gray-600 text-left">
                                                {{ $role->description }}
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-slot>

                <x-slot name="actions">
                    <x-action-message class="mr-3" on="saved">
                        {{ __('Added.') }}
                    </x-action-message>

                    <x-button>
                        {{ __('Add') }}
                    </x-button>
                </x-slot>
            </x-form-section>
        </div>
    @endif

    @if ($project->allMembers()->isNotEmpty())
        <x-section-border />

        <!-- Manage Project Members -->
        <div class="mt-10 sm:mt-0">
            <x-action-section>
                <x-slot name="title">
                    {{ __('Project Members') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('All of the people that are part of this project, including team members.') }}
                </x-slot>

                <!-- Project Member List -->
                <x-slot name="content">
                    <div class="space-y-6">
                        @foreach ($project->allMembers()->sortBy('name') as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="w-8 h-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                    <div class="ml-4">{{ $user->name }}</div>
                                </div>

                                <div class="flex items-center">
                                    <!-- Show Project Member Role -->
                                    @if (Laravel\Jetstream\Jetstream::hasRoles())
                                        <div class="ml-2 text-sm text-gray-400">
                                            {{ $user->membership->role_label }} / {{ $user->membership->source }}
                                        </div>
                                    @endif

                                    @unless ($user->is(optional($this->project->owner)->user))
                                        <!-- Leave Project -->
                                        @if ($this->user->id === $user->id && $user->membership->isProjectMember())
                                            <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="$toggle('confirmingLeavingProject')">
                                                {{ __('Leave') }}
                                            </button>

                                        <!-- Remove Project Member -->
                                        @elseif (Gate::check('removeProjectMember', $project) && $user->membership->isProjectMember())
                                            <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmProjectMemberRemoval('{{ $user->id }}')">
                                                {{ __('Remove') }}
                                            </button>
                                        @endif
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-action-section>
        </div>
    @endif

    <!-- Role Management Modal -->
    <x-dialog-modal wire:model.live="currentlyManagingRole">
        <x-slot name="title">
            {{ __('Manage Role') }}
        </x-slot>

        <x-slot name="content">
            <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                @foreach ($this->roles as $index => $role)
                    <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 {{ $index > 0 ? 'border-t border-gray-200 rounded-t-none' : '' }} {{ ! $loop->last ? 'rounded-b-none' : '' }}"
                                    wire:click="$set('currentRole', '{{ $role->key }}')">
                        <div class="{{ $currentRole !== $role->key ? 'opacity-50' : '' }}">
                            <!-- Role Name -->
                            <div class="flex items-center">
                                <div class="text-sm text-gray-600 {{ $currentRole == $role->key ? 'font-semibold' : '' }}">
                                    {{ $role->name }}
                                </div>

                                @if ($currentRole == $role->key)
                                    <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <!-- Role Description -->
                            <div class="mt-2 text-xs text-gray-600">
                                {{ $role->description }}
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="stopManagingRole" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ml-2" wire:click="updateRole" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Leave Project Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingLeavingProject">
        <x-slot name="title">
            {{ __('Leave Project') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to leave this project?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingLeavingProject')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-2" wire:click="leaveProject" wire:loading.attr="disabled">
                {{ __('Leave') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Remove Project Member Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingProjectMemberRemoval">
        <x-slot name="title">
            {{ __('Remove Project Member') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this person from the project?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingProjectMemberRemoval')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-2" wire:click="removeProjectMember" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
