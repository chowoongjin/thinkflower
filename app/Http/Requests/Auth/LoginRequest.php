<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'login_id.required' => '아이디를 입력해 주세요.',
            'password.required' => '비밀번호를 입력해 주세요.',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $remember = $this->boolean('remember');

        if (!Auth::attempt($this->only('login_id', 'password'), $remember)) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'login_id' => '아이디 또는 비밀번호가 올바르지 않습니다.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => '로그인 시도가 너무 많습니다. ' . $seconds . '초 후 다시 시도해 주세요.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower((string) $this->input('login_id')) . '|' . $this->ip();
    }
}
