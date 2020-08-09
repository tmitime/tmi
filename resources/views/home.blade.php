@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full text-center">
        <h2 class="mt-6 mb-4 text-3xl leading-9 font-extrabold text-gray-200">{{ __('Dashboard') }}</h2>
  
        <div class="">
            @if (session('status'))
                <div class="bg-green-500 text-white p-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{ __('You are logged in!') }}
        </div>
    </div>
</div>
@endsection
