<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create(DashboardService $dashboard): View
    {
        // Kalau perhitungan gagal (mis. database belum dimigrasi), halaman login tetap tampil.
        $summary = rescue(fn () => $dashboard->loginSummary(), [
            'receivable' => 0,
            'trend' => null,
            'bars' => [],
            'attention_count' => 0,
            'attention_remaining' => 0,
        ]);

        return view('auth.login', ['summary' => $summary]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->only('username', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['credentials' => 'Username atau password yang Anda masukkan salah. Silakan periksa kembali.'])
            ->onlyInput('username');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
