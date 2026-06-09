<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('auth.login', compact('users'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'password' => 'required',
        ]);

        $user = User::find($request->integer('user_id'));

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'user_id' => 'بەکارهێنەر یان وشەی نهێنی هەڵەیە.',
        ])->onlyInput('user_id');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /* ---------------- Password reset ---------------- */

    public function showForgotPassword()
    {
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('auth.forgot-password', compact('users'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        $user = User::find($request->integer('user_id'));

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
        }

        // Always return the same message so visitors cannot probe which accounts exist.
        return back()->with('success', 'ئەگەر هەژمارەکە هەبێت، بەستەری گۆڕینی وشەی نهێنی نێردرا بۆ ئیمەیڵەکەت.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'وشەی نهێنیت بە سەرکەوتوویی گۆڕدرا. ئێستا بچۆ ژوورەوە.');
        }

        return back()->withErrors([
            'email' => 'نەتوانرا وشەی نهێنی بگۆڕدرێت. بەستەرەکە لەوانەیە بەسەرچووبێت.',
        ])->onlyInput('email');
    }
}
