<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $loginInput = $request->input('username') ?? $request->input('email');

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (empty($loginInput)) {
            throw ValidationException::withMessages([
                'email' => 'Username atau email wajib diisi.',
                'username' => 'Username atau email wajib diisi.',
            ]);
        }

        $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->boolean('remember');

        // Cari pengguna berdasarkan email, nama, atau username prefix
        $user = User::query()
            ->where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->orWhereRaw("SUBSTRING_INDEX(email, '@', 1) = ?", [$loginInput])
            ->when($loginInput === 'andi.owner', function ($q) {
                $q->orWhere('email', 'andi@suksesabadi.com');
            })
            ->first();

        $attemptSuccess = false;
        if ($user) {
            $attemptSuccess = Auth::attempt([
                'email' => $user->email,
                'password' => $request->string('password'),
            ], $remember);
        }

        if (! $attemptSuccess) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Username atau password yang Anda masukkan salah. Silakan periksa kembali.',
                'username' => 'Username atau password yang Anda masukkan salah. Silakan periksa kembali.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Proses logout pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar.');
    }
}
