<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Identity;
use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Auth\LoginOrRegisterSocialiteUser;

class ConnectExternalAuthenticationController extends Controller
{
    use LoginOrRegisterSocialiteUser;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Redirect the user to the Authentication provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::with('gitlab')
            ->scopes([
                'openid',
                'read_api',
            ])
            ->redirectUrl(route('connect.callback', ['provider' => 'gitlab']))
            ->redirect();
    }

    /**
     * Obtain the user information from Authentication provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::with('gitlab')
            ->scopes([
                'openid',
                'read_api',
            ])
            ->redirectUrl(route('connect.callback', ['provider' => 'gitlab']))
            ->user();

        // if user denies the authorization request we get
        // GuzzleHttp\Exception\ClientException
        // Client error: `POST https://gitlab.com/oauth/token` resulted in a `401 Unauthorized` response: {"error":"invalid_grant","error_description":"The provided authorization grant is invalid, expired, revoked, does not ma (truncated...)

        $loggedIn = Auth::check();
        
        $registeredUser = $this->connect('gitlab', $user);

        if($registeredUser->wasRecentlyCreated){
            event(new Registered($registeredUser));
        }
        
        if(!$loggedIn){
            Auth::login($registeredUser);
            event(new Login(Auth::guard(), $registeredUser, false));
        }
        
        return redirect()->route('home');
    }
}
