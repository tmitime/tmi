<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Identity;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class ConnectExternalAuthenticationController extends Controller
{
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

        

        /**
         * @var App\User
         */
        $localUser = Auth::user();
        $loggedIn = Auth::check();

        // if not already logged in and the provider do not share the email
        // address then we don't have a way to identify the user so abort
        abort_unless(!$loggedIn && $user->getEmail(), '422', 'Could not get email address');

        $registeredUser = DB::transaction(function() use ($localUser, $user){
    
            if(!$localUser){
                // if user not logged-in then register the new user
                $localUser = User::findFromIdentityOrCreate($user->getEmail(), 'gitlab', $user->getId(), [
                    'name' => $user->getName() ?? $user->getNickname(),
                    'email_verified_at' => now(),
                ]);
            }
    
            $localUser->identities()->updateOrCreate([
                    'provider'=>'gitlab', 
                    'provider_id'=> $user->getId()
                ],
                [
                    'token'=> $user->token,
                    'refresh_token'=> $user->refreshToken,
                    'expires_at'=> $user->expiresIn ? now()->addSeconds($user->expiresIn) : null
                ]);

            return $localUser;
        });
        
        if(!$loggedIn){
            Auth::login($registeredUser);
        }
        
        return redirect()->route('home');
    }
}
