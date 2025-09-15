<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // Tambahkan ini
use App\Providers\RouteServiceProvider; // Tambahkan ini

class CustomAuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $requestedRole = $request->input('role'); // Ambil role dari input form login

        // Mencegah login ke halaman yang salah
        // Jika ada role yang diminta dari form login (misal: /login/admin atau /login/kasir)
        // dan role user yang login tidak sesuai dengan role yang diminta,
        // maka logout user dan kembalikan dengan error.
        if ($requestedRole && $user->role !== $requestedRole) {
            Auth::logout(); // Logout user yang salah role
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Anda tidak memiliki akses sebagai ' . ucfirst($requestedRole) . '.',
            ]);
        }

        // Redirect berdasarkan role user yang sebenarnya
        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        } elseif ($user->role === 'kasir') {
            return redirect()->intended(route('pos.index'));
        }

        // Fallback jika role tidak dikenali (seharusnya tidak terjadi jika role sudah ditentukan)
        return redirect()->route('pos.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
