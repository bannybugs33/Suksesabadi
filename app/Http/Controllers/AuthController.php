<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
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
        // Hanya angka agregat toko (tanpa nama pihak) karena pengunjung belum login.
        // rescue(): jika perhitungan gagal (mis. database belum dimigrasi), halaman login tetap tampil.
        return view('auth.login', [
            'pengingat' => rescue(
                fn () => Kuitansi::ringkasanPengingat(),
                ['jumlah' => 0, 'terlambat' => 0, 'sisa' => 0.0]
            ),
            'grafikPiutang' => rescue(
                fn () => Kuitansi::grafikPiutangBulanan(),
                ['bars' => [], 'total' => 0.0, 'perubahan' => null]
            ),
        ]);
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

        // Cari pengguna berdasarkan email, nama, atau username (bagian sebelum '@').
        // Memakai LIKE ... ESCAPE (bukan SUBSTRING_INDEX yang hanya ada di MySQL) agar jalan di SQLite.
        $escapedLogin = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], (string) $loginInput);

        $user = User::query()
            ->where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->orWhereRaw("email LIKE ? ESCAPE '!'", [$escapedLogin.'@%'])
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

        // Dibaca sekali oleh dashboard untuk menampilkan notifikasi pengingat jatuh tempo.
        return redirect()->intended(route('dashboard'))
            ->with('pengingat_login', rescue(
                fn () => Kuitansi::ringkasanPengingat(Auth::id()),
                ['jumlah' => 0, 'terlambat' => 0, 'sisa' => 0.0]
            ));
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
