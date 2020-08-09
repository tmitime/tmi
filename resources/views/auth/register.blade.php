@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full text-center">
        <h2 class="mt-6 mb-4 text-3xl leading-9 font-extrabold text-gray-200">{{ __('Register') }}</h2>

        <div class="mt-6">
            <a href="{{ route('connect.provider', ['provider' => 'gitlab']) }}"  class="relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                {{ __('Register with Gitlab.com') }}
            </a>
        </div>

        <div class="mt-6">
            {{ __('or register using an email address') }}
        </div>
    
        <div class="mt-6">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="">
                    <label for="name" class="mb-1">{{ __('Name') }}</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-input w-full @error('name') border-red-500 shadow-outline-red @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <p class="p-2 bg-red-700 text-white" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <label for="email" class="mb-1">{{ __('E-Mail Address') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-input w-full @error('email') border-red-500 shadow-outline-red @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                        @error('email')
                            <p class="p-2 bg-red-700 text-white" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <label for="password" class="mb-1">{{ __('Password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-input w-full @error('password') border-red-500 shadow-outline-red @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <p class="p-2 bg-red-700 text-white" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <label for="password-confirm" class="mb-1">{{ __('Confirm Password') }}</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-input w-full" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                    {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
