<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
     * Redirect user after authentication.
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect('/'); // Cambia '/' por la ruta a la que quieras redirigir
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Redirigir al home después de hacer login
        return redirect()->intended(RouteServiceProvider::HOME);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Cierra la sesión
        Auth::guard('web')->logout();

        // Invalida la sesión
        $request->session()->invalidate();

        // Regenera el token CSRF para evitar ataques de tipo CSRF
        $request->session()->regenerateToken();

        // Redirige al home después del logout
        return redirect('/');
    }
}
