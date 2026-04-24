<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    use AuthenticatesUsers {
        incrementLoginAttempts as protected traitIncrementLoginAttempts;
        clearLoginAttempts as protected traitClearLoginAttempts;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.x'
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function maxAttempts()
    {
        return 10;
    }

    protected function decayMinutes()
    {
        return 15;
    }

    protected function incrementLoginAttempts(Request $request)
    {
        $this->traitIncrementLoginAttempts($request);

        $user = User::where('email', $request->input($this->username()))->first();

        if ($user) {
            $user->failed_attempts = min((int) $user->failed_attempts + 1, $this->maxAttempts());
            $user->save();
        }
    }

    protected function clearLoginAttempts(Request $request)
    {
        $this->traitClearLoginAttempts($request);

        $user = User::where('email', $request->input($this->username()))->first();

        if ($user) {
            $user->failed_attempts = 0;
            $user->locked_until = null;
            $user->save();
        }
    }

    protected function sendLockoutResponse(Request $request)
    {
        $user = User::where('email', $request->input($this->username()))->first();

        if ($user) {
            $user->locked_until = now()->addMinutes($this->decayMinutes());
            $user->save();
        }

        $seconds = $this->limiter()->availableIn($this->throttleKey($request));
        $minutes = (int) ceil($seconds / 60);

        throw ValidationException::withMessages([
            $this->username() => ["Muitas tentativas de login. Tente novamente em {$minutes} minuto(s)."],
        ]);
    }
}
