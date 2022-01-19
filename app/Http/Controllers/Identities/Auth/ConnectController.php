<?php

namespace App\Http\Controllers\Identities\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Oneofftech\Identities\Auth\ConnectUserIdentity;

class ConnectController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Connect Identity Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the connection (or update) of an
    | identity for an already authenticated user.
    | The controller uses a trait to conveniently provide its
    | functionality to your applications.
    |
    */

    use ConnectUserIdentity;

    protected $attributes = ['b'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function connected($user, $identity, array $attributes, Request $request)
    {
        $b = $attributes['b'] ?? null;

        if ($b === 'profile') {
            return redirect()->route('profile.show');
        }

        return redirect($this->getPreviousUrl());
    }
}
