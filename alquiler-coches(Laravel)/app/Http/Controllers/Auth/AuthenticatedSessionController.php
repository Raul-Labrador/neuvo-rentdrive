<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller {
    function create(): View {
        return view('auth.login');
    }

    function store(LoginRequest $request): RedirectResponse {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Tras cerrar sesión en Laravel, redirige a WordPress para que también
     * cierre su sesión, y luego aterriza en la home de WordPress.
     */
    function destroy(Request $request): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Generar token firmado para el logout en WordPress
        $secret    = env('LARAVEL_SSO_SECRET', '');
        $timestamp = time();
        $payload   = 'logout|' . $timestamp;
        $signature = hash_hmac('sha256', $payload, $secret);
        $token     = base64_encode($payload . '|' . $signature);

        $wpLogoutUrl = rtrim(env('WP_URL', 'https://neuvo-app.com/'), '/')
            . '/wp-logout-sso?token=' . urlencode($token);

        return redirect()->away($wpLogoutUrl);
    }
}
