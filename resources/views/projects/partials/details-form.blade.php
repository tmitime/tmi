<div>
    <x-label for="name" value="{{ __('Name') }}" />
    <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', optional($project ?? null)->name)" required autofocus />
    <x-input-error for="name" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="description" value="{{ __('Description') }}" />
    <x-textarea id="description" class="block mt-1 w-full h-40" type="text" name="description">{{ old('description', optional($project ?? null)->description) }}</x-textarea>
    <x-input-error for="description" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="start_at" value="{{ __('Start date') }}" />
    <x-input id="start_at" class="block mt-1 w-full" type="date" name="start_at" :value="old('start_at', optional(optional($project ?? null)->start_at)->toDateString())" required />
    <x-input-error for="start_at" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="end_at" value="{{ __('End date') }}" />
    <x-input id="end_at" class="block mt-1 w-full" type="date" name="end_at" :value="old('end_at', optional(optional($project ?? null)->end_at)->toDateString())" />
    <x-input-error for="end_at" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="working_days" value="{{ __('Working days') }}" />
    <x-input id="working_days" class="block mt-1 w-full" type="number" name="working_days" :value="old('working_days', optional($project ?? null)->working_days)" min="1" />
    <x-input-error for="working_days" class="mt-2" />
</div>
