<?php

namespace App\Auth;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;

trait LoginOrRegisterSocialiteUser
{
    /**
     * Register or login a user by connecting
     * the identity provider
     * 
     * 
     */
    public function connect($provider, SocialiteUser $user): User
    {        
        /**
         * @var App\User
         */
        $localUser = Auth::user();

        // if not already logged in and the provider do not share the email
        // address then we don't have a way to identify the user so abort
        abort_unless($user->getEmail(), '422', 'Could not get email address');

        $registeredUser = DB::transaction(function() use ($localUser, $user, $provider){
    
            if(!$localUser){
                // if user not logged-in then register the new user
                $localUser = User::findFromIdentityOrCreate($user->getEmail(), $provider, $user->getId(), [
                    'name' => $user->getName() ?? $user->getNickname(),
                    'email_verified_at' => now(),
                ]);
            }
    
            $localUser->identities()->updateOrCreate([
                    'provider'=> $provider, 
                    'provider_id'=> $user->getId()
                ],
                [
                    'token'=> $user->token,
                    'refresh_token'=> $user->refreshToken,
                    'expires_at'=> $user->expiresIn ? now()->addSeconds($user->expiresIn) : null
                ]);

            return $localUser;
        });

        return $registeredUser;
    }
}
