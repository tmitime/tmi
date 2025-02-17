<div @isset ($create) class="mt-4" @endisset>
    <x-label for="duration" value="{{ __('Duration (minutes)') }}" />
    <x-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration', optional($task ?? null)->duration)" min="1" :autofocus="isset($create)" />
    <x-input-error for="duration" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="description" value="{{ __('Description') }}" />
    <x-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description', optional($task ?? null)->description)" required :autofocus="!isset($create)" />
    <x-input-error for="description" class="mt-2" />
</div>

<div class="mt-4">
    <x-label for="created_at_date" value="{{ __('Done on') }}" />
    
    <div class="flex space-x-2">
        <div class="w-1/3">
            <x-input id="created_at_date" class="block mt-1 w-full" type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" name="created_at_date" :value="old('created_at_date', optional(optional($task ?? null)->created_at)->toDateString()) ?? now()->toDateString()" required />
            <x-input-error for="created_at_date" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">{{ __('Format:') }} <code class="">YYYY-MM-DD</code></p>
        </div>
        <div class="w-1/3">
            <x-input id="created_at_time" class="block mt-1 w-full" type="time" step="15" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" name="created_at_time" :value="old('created_at_time', optional(optional($task ?? null)->created_at)->format('H:i:s')) ?? now()->format('H:i:s')" required />
            <x-input-error for="created_at_time" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">{{ __('Format:') }} <code class="">HH:MM:SS</code></p>
        </div>
    </div>

    <p class="mt-1 text-sm text-gray-600">{{ __('Indicate the approximate date and time when the task was completed') }}</p>
</div>

<div class="mt-4">
    <x-label for="type" value="{{ __('Type') }}" />
    <x-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type', optional($task ?? null)->type)" />
    <x-input-error for="type" class="mt-2" />
</div>

