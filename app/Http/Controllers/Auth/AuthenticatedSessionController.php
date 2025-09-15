<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // Tambahkan ini

class AuthenticatedSessionController extends Controller
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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $role = $request->input('role'); // ambil role dari form

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // cek role user
        if ($role && Auth::user()->role !== $role) {
            Auth::logout();
            throw ValidationException::withMessages([ // Gunakan ValidationException
                'email' => 'Anda tidak memiliki akses sebagai ' . $role,
            ]);
        }

        // Redirect berdasarkan role user yang sebenarnya
        if (Auth::user()->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        } elseif (Auth::user()->role === 'kasir') {
            return redirect()->intended(route('pos.index'));
        }

        // Fallback jika role tidak dikenali
        return redirect()->intended(RouteServiceProvider::HOME);
    }


    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        } elseif ($user->role === 'kasir') {
            return redirect()->route('pos.index');
        }

        abort(403);
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
