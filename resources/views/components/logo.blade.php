<a class="text-xl font-bold leading-none" href="{{ auth()->check() ? route('home') : url('/') }}">
    {{ config('app.name', 'TMI') }}
</a>