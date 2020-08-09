<nav class="shadow-lg">
    <div class="p-4 flex justify-between">
        
        <x-logo />

        <div class="">
            <ul class="flex">
                
                @guest
                    <li class="">
                        <a class="p-2 hover:text-indigo-300" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="">
                            <a class="p-2 hover:text-indigo-300" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="flex">
                        <a class="" href="#" role="button">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="ml-4">
                            <a class="hover:text-indigo-300" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>