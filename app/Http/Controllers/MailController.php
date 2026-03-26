<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\MailModel;
use App\Models\User;

class MailController extends Controller
{
    function send(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        // Check if user is OAuth (has google_id)
        if (!empty($user->google_id)) {
            return back()->withErrors(['email' => 'This account was created with Google OAuth. You cannot reset the password.']);
        }

        // Create encrypted token containing email and timestamp
        $tokenData = [
            'email' => $request->email,
            'created_at' => now()->timestamp
        ];
        $token = Crypt::encryptString(json_encode($tokenData));

        $mailData = [
            'email' => $request->email,
            'token' => $token,
            'reset_link' => route('password.reset', $token),
        ];

        Mail::to($request->email)->send(new MailModel($mailData));
        return redirect()->route('login')->with('success', 'Password reset request sent successfully! Please check your email.');
    }

    function reset(Request $request) {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Decrypt the token to get the email
            $tokenData = json_decode(Crypt::decryptString($request->token), true);
            $email = $tokenData['email'];

            // Check if user still exists
            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('password.request')->withErrors(['email' => 'This password reset link is invalid.']);
            }

            // Check if token is not too old (24 hours)
            if (now()->timestamp - $tokenData['created_at'] > 86400) {
                return redirect()->route('password.request')->withErrors(['email' => 'This password reset link has expired.']);
            }

            // Update the user's password
            $user->password = bcrypt($request->password);
            $user->save();

            return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in with your new password.');
        } catch (\Exception $e) {
            return redirect()->route('password.request')->withErrors(['email' => 'This password reset link is invalid.']);
        }
    }

}
