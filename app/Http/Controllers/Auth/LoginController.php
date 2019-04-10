<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Another authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToAnotherProvider()
    {
        return Socialite::driver('another')->redirect();
    }

    /**
     * Obtain the user information from Another.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleAnotherProviderCallback()
    {
        $user = Socialite::driver('another')->user();

        $existUser = User::where([
            ['openid','=',$user->getId()],
            ['platform','=','another'],
        ])->first();
        if($existUser){
            \Auth::login($existUser);
            return redirect('/home');
        }

        $user = User::create([
            'openid' => $user->getId(),
            'name' => $user->getNickname(),
            'email'=> $user->getEmail(),
            'password' => bcrypt('12345678'),
            'platform' => 'another',
            'email_verified_at' => now()
        ]);

        \Auth::login($user);

        return redirect('/home');
    }
}
