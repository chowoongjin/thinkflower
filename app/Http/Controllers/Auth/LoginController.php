<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('pages.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user) {
            $user->forceFill([
                'last_login_at' => now(),
            ])->save();
        }

        $response = redirect()->intended('/');

        if ($request->boolean('remember') && $request->filled('login_id')) {
            $response->withCookie(
                cookie('saved_login_id', $request->input('login_id'), 60 * 24 * 30)
            );
        } else {
            $response->withoutCookie('saved_login_id');
        }

        return $response;
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
