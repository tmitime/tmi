@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full text-center">
        <h2 class="mt-6 mb-4 text-3xl leading-9 font-extrabold text-gray-200">{{ __('Login') }}</h2>

        <div class="mt-6">
            <a href="{{ route('connect.provider', ['provider' => 'gitlab']) }}"  class="relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                {{ __('Login with Gitlab') }}
            </a>
        </div>

        <div class="mt-6">
            {{ __('or login using email and password') }}
        </div>
    
        <div class="mt-6">
            <form method="POST" action="{{ route('login') }}">
                @csrf
    
                <div class="">
                    <label for="email" class="mb-1">{{ __('E-Mail Address') }}</label>
    
                    <div class="">
                        <input id="email" type="email" class="form-input w-full @error('email') border-red-500 shadow-outline-red @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
    
                        @error('email')
                            <p class="p-2 bg-red-700 text-white" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
    
                <div class="mt-2">
                    <label for="password" class="mb-1">{{ __('Password') }}</label>
    
                    <div class="">
                        <input id="password" type="password" class="form-input w-full @error('password') border-red-500 shadow-outline-red @enderror" name="password" required autocomplete="current-password">
    
                        @error('password')
                            <p class="p-2 bg-red-700 text-white" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
    
                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                    <label for="remember" class="ml-2 block text-sm leading-5 text-gray-100">
                        {{ __('Remember Me') }}
                    </label>
                    </div>
    
                    @if (Route::has('password.request'))
                    <div class="text-sm leading-5">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-500 hover:text-indigo-400 focus:outline-none focus:underline transition ease-in-out duration-150">
                        {{ __('Forgot Your Password?') }}
                    </a>
                    </div>
                    @endif
                </div>
    
                <div class="mt-6">
                    <button type="submit" class="relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                    {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
