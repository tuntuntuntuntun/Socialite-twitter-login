<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Illuminate\Support\Facades\Auth;
use App\User;

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

    // OAuth認証先にリダイレクト
    public function redirectToProvider($provider)
    {
        // login/{provider}のprovider部分の文字列が渡される
        return Socialite::driver($provider)->redirect();

    }

    // OAuth認証の結果を受け取る
    public function handleProviderCallback($provider)
    {

        try {
            $providerUser = \Socialite::with($provider)->user();

        } catch(\Exception $e) {
            return redirect('/login')->with('oauth_error', '予期せぬエラーが発生しました');

        }

        if ($email = $providerUser->getEmail()) {
            Auth::login(User::firstOrCreate([
                'email' => $email
            ],
            [
                'name' => $providerUser->getName()
            ]));

            return redirect($this->redirectTo);

        } else {
            return redirect('/login')->with('oauth_error', 'メールアドレスが取得できませんでした');
        }
    }
}
