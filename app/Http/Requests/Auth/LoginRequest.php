<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        Log::info('=== LOGIN REQUEST START ===');
        Log::info('Email:', ['email' => $this->email]);

        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // ✅ DETAILED USER CHECK
        $user = \App\Models\User::where('email', $this->email)->first();

        Log::info('User details:', [
            'exists' => !is_null($user),
            'id' => $user ? $user->id : null,
            'name' => $user ? $user->name : null,
            'role' => $user ? $user->role : null,
            'agency_id' => $user ? $user->agency_id : null,
            'password_hash' => $user ? $user->password : 'no user',
            'created_at' => $user ? $user->created_at : null
        ]);

        if ($user) {
            // ✅ MANUAL PASSWORD VERIFICATION
            $passwordCheck = \Illuminate\Support\Facades\Hash::check($this->password, $user->password);
            Log::info('Manual password check:', [
                'input_password' => $this->password,
                'input_length' => strlen($this->password),
                'hash_match' => $passwordCheck
            ]);

            // ✅ CEK HASH INFO
            Log::info('Hash info:', [
                'hash' => $user->password,
                'hash_length' => strlen($user->password),
                'hash_starts_with' => substr($user->password, 0, 10)
            ]);
        }

        Log::info('Attempting Auth::attempt...');

        if (! Auth::attempt($credentials, $remember)) {
            Log::warning('Auth::attempt FAILED - Possible reasons:');
            Log::warning('1. Password mismatch');
            Log::warning('2. User not found');
            Log::warning('3. Hash algorithm issue');

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        Log::info('Auth::attempt SUCCESS');
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
