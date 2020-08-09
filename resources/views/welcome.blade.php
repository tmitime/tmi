@extends('layouts.app')

@section('content')

    <div class="flex flex-col items-center">

        <h1 class="text-6xl text-gray-50 leading-none">{{ config('app.name', 'TMI') }}</h1>
        <h2 class="text-2xl text-cool-gray-400">{{ __('time tracking') }}</h2>

    </div>

@endsection
