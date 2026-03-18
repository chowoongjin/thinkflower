<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('pages.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login_id.required' => '아이디를 입력해 주세요.',
            'password.required' => '비밀번호를 입력해 주세요.',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt([
            'login_id' => $credentials['login_id'],
            'password' => $credentials['password'],
        ], $remember)) {
            return back()->withErrors([
                'login_id' => '아이디 또는 비밀번호가 올바르지 않습니다.',
            ])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'last_login_at' => now(),
            ])->save();
        }

        $response = redirect()->intended('/');

        if ($remember && $request->filled('login_id')) {
            $response->withCookie(cookie('saved_login_id', $request->login_id, 60 * 24 * 30));
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
