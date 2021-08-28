<div>
    <x-jet-label for="name" value="{{ __('Name') }}" />
    <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', optional($project ?? null)->name)" required autofocus />
    <x-jet-input-error for="name" class="mt-2" />
</div>

<div class="mt-4">
    <x-jet-label for="start_at" value="{{ __('Start date') }}" />
    <x-jet-input id="start_at" class="block mt-1 w-full" type="date" name="start_at" :value="old('start_at', optional(optional($project ?? null)->start_at)->toDateString())" required />
    <x-jet-input-error for="start_at" class="mt-2" />
</div>

<div class="mt-4">
    <x-jet-label for="end_at" value="{{ __('End date') }}" />
    <x-jet-input id="end_at" class="block mt-1 w-full" type="date" name="end_at" :value="old('end_at', optional(optional($project ?? null)->end_at)->toDateString())" />
    <x-jet-input-error for="end_at" class="mt-2" />
</div>

<div class="mt-4">
    <x-jet-label for="working_days" value="{{ __('Working days') }}" />
    <x-jet-input id="working_days" class="block mt-1 w-full" type="number" name="working_days" :value="old('working_days', optional($project ?? null)->working_days)" min="1" />
    <x-jet-input-error for="working_days" class="mt-2" />
</div>
