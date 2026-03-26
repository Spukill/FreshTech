<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


class GoogleOAuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle() {

        $google_user = Socialite::driver('google')->stateless()->user();
        $user = User::where('google_id', $google_user->getId())->first();
        

        // If the user does not exist, create one
        if (!$user) {

            $new_user = User::create([
                'email' => $google_user->getEmail(),
                // create a random password
                'password' => bcrypt(Str::random(40)),
                'google_id' => $google_user->getId(),
            ]);

            // create buyer profile and store the display name there
            $new_user->buyer()->create([
                'user_name' => $google_user->getName() ?? 'Google User',
                'exp' => 0,
            ]);

            Auth::login($new_user);

        // Otherwise, simply log in with the existing user
        } else {
            Auth::login($user);
        }

        // After login, redirect to homepage
        return redirect('/');
    }
}
