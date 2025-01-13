<div class="md:grid md:grid-cols-3 md:gap-6">
    <x-section-title>
        <x-slot name="title">{{ __('Identity providers') }}</x-slot>
        <x-slot name="description">{{ __('Manage the connection and data coming from third party identity providers used to log-in and register.') }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">

            <h3 class="text-lg font-medium text-gray-900">
                @if ($this->hasIdentities)
                    {{ __('You have external identities connected.') }}
                @else
                    {{ __('You have not connected external identity providers to your account.') }}
                @endif
            </h3>

            <div class="mt-3 ">

                @unless ($hasIdentities)
                    
                    <x-oneofftech-identity-link  label="{{ __('Connect your Gitlab profile') }}" action="connect" provider="gitlab" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"/>

                    <x-input-error for="gitlab" class="mt-2" />

                @endunless


                @foreach ($identities as $identity)
                    <div class="py-3 mb-2 flex justify-between" >

                        <div class="w-4/6">
                            <p class="font-bold">
                                {{ \Illuminate\Support\Str::ucfirst($identity->provider) }}
                            </p>
                
                            <p class="text-sm text-gray-600">
                                {{ __('Connected on') }}
                                
                                <x-time :time="$identity->created_at" />
                            </p>
                
                            @if ($identity->registration)
                                <p class="text-sm text-gray-600">
                                    {{ __('Identity used for creating the account') }}
                                </p>
                            @endif
                        </div>

                        <div class="w-1/6">
                            <x-oneofftech-identity-link action="connect" label="{{ __('Link again') }}" :parameters="['b' => 'profile']" :provider="$identity->provider" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"/>
                        </div>
                
                    </div>
                
                @endforeach
                
            </div>
        </div>
    </div>
</div>
