<?php

namespace App\Http\Controllers\Identities\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Oneofftech\Identities\Auth\RegistersUsersWithIdentity;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register via Identity Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users via identities
    | provided by third party authentication services. The controller
    | uses a trait to conveniently provide its functionality.
    |
    */

    use RegistersUsersWithIdentity;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\User
     */
    protected function create(array $data)
    {
        $generated = Str::random(20);
        $input = array_merge($data, ['password' => $generated, 'password_confirmation' => $generated]);
        return app(CreateNewUser::class)->create($input);
    }
}
