@props(['value'])

<div {{ $attributes->merge(['class' => 'prose']) }}>
    {!! \Illuminate\Support\Str::markdown($value) !!}
</div>