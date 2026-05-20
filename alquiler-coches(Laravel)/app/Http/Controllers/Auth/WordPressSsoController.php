<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WordPressSsoController extends Controller {
    //Tiempo máximo en segundos que el token SSO es válido.
    protected int $tokenTtl = 120;

    /*
     * Recibe el token SSO generado por WordPress, lo verifica y autentica
     * al administrador en Laravel sin necesidad de introducir contraseña.
     *
     * Flujo:
     *  WordPress hace login y genera: base64( email | timestamp | HMAC-SHA256 )
     *  Laravel decodifica, comprueba la firma y el tiempo de expiración.
     *  Si es válido y el usuario existe en la BD de Laravel, se hace login.
     *  Se redirige al dashboard.
     */
    function handle(Request $request) {
        $rawToken = $request->query('token');

        Log::info('[WP-SSO] Intento de autenticación SSO', [
            'ip'       => $request->ip(),
            'hasToken' => !empty($rawToken),
        ]);

        if (empty($rawToken)) {
            Log::warning('[WP-SSO] Token no proporcionado');
            return redirect()->route('login')
                ->withErrors(['email' => 'Acceso SSO inválido. Por favor inicia sesión manualmente.']);
        }

        // base64_decode puede fallar si el token viene con padding URL-encoded
        $decoded = base64_decode(str_replace(' ', '+', $rawToken), true);

        if ($decoded === false) {
            Log::warning('[WP-SSO] base64_decode falló', ['raw' => $rawToken]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Token SSO inválido.']);
        }

        $parts = explode('|', $decoded);

        if (count($parts) !== 3) {
            Log::warning('[WP-SSO] Formato incorrecto', ['parts' => count($parts), 'decoded' => $decoded]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Formato de token SSO incorrecto.']);
        }

        [$email, $timestamp, $signature] = $parts;

        Log::info('[WP-SSO] Token decodificado', [
            'email'     => $email,
            'timestamp' => $timestamp,
            'age_secs'  => time() - (int) $timestamp,
        ]);

        // Comprobamos la expiración
        if ((time() - (int) $timestamp) > $this->tokenTtl) {
            Log::warning('[WP-SSO] Token expirado', ['age' => time() - (int) $timestamp]);
            return redirect()->route('login')
                ->withErrors(['email' => 'El enlace de acceso ha expirado. Por favor inicia sesión en WordPress de nuevo.']);
        }

        // Leer la secret key directamente del entorno (no usar config() cacheado)
        $secret = env('LARAVEL_SSO_SECRET', '');

        if (empty($secret)) {
            Log::error('[WP-SSO] LARAVEL_SSO_SECRET no está configurado en .env');
            return redirect()->route('login')
                ->withErrors(['email' => 'Error de configuración SSO. Contacta al administrador.']);
        }

        // Verificar firma HMAC
        $payload  = $email . '|' . $timestamp;
        $expected = hash_hmac('sha256', $payload, $secret);

        Log::info('[WP-SSO] Verificando firma', [
            'expected'  => $expected,
            'received'  => $signature,
            'match'     => hash_equals($expected, $signature),
        ]);

        if (!hash_equals($expected, $signature)) {
            Log::warning('[WP-SSO] Firma HMAC inválida');
            return redirect()->route('login')
                ->withErrors(['email' => 'Token SSO inválido. Por favor inicia sesión manualmente.']);
        }

        // Buscar usuario en la BD de Laravel por email, o crearlo automáticamente
        // Ya que el token viene firmado por WordPress, sabemos con certeza que es un admin legítimo.
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => ucfirst(explode('@', $email)[0]),
                'password' => bcrypt(\Illuminate\Support\Str::random(24)),
            ]
        );

        if ($user->wasRecentlyCreated) {
            Log::info('[WP-SSO] Nuevo usuario admin creado automáticamente desde SSO', ['email' => $email]);
        }

        Log::info('[WP-SSO] Autenticando usuario', ['id' => $user->id, 'email' => $user->email]);

        // Autenticar en Laravel
        Auth::login($user, remember: true);

        $request->session()->regenerate();

        Log::info('[WP-SSO] Login SSO exitoso', ['user_id' => $user->id]);

        return redirect()->route('dashboard');
    }

    // Cierra la sesión en Laravel tras el logout en WordPress.
    function logout(Request $request) {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::info('[WP-SSO] Logout SSO exitoso desde WordPress');

        return redirect(env('WP_URL', 'https://neuvo-app.com/'));
    }
}
