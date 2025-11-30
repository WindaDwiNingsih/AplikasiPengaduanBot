<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // DEBUG: Log the password before hashing
        Log::info('Password update debug:', [
            'raw_password' => $validated['password'],
            'password_length' => strlen($validated['password']),
            'user_id' => $request->user()->id,
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // DEBUG: Log after update
        Log::info('Password updated:', [
            'user_id' => $request->user()->id,
            'new_hash' => $request->user()->password,
        ]);

        return back()->with('status', 'password-updated');
    }
}

