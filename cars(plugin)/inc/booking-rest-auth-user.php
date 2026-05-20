<?php
defined('ABSPATH') or die('No script kiddies please!');

if (!function_exists('booking_get_client_ip')) {
    function booking_get_client_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

add_action('rest_api_init', function () {
    register_rest_route('booking/v1', '/auth-user', array(
        'methods'             => 'POST',
        'callback'            => 'rlp_booking_rest_auth_user',
        'permission_callback' => '__return_true',
    ));
});

function rlp_booking_rest_auth_user($request) {
    $nonce = $request->get_header('X-WP-Nonce');

    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid security token.'
        ], 403);
    }

    // Rate limit: max 10 requests per 10 minutes per IP
    $ip = booking_get_client_ip();
    $ip_key = 'booking_auth_user_' . md5($ip);
    $ip_count = (int) get_transient($ip_key);

    if ($ip_count >= 10) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Too many login attempts. Please try again later.'
        ], 429);
    }

    set_transient($ip_key, $ip_count + 1, 10 * MINUTE_IN_SECONDS);

    $params = $request->get_json_params();
    $email  = isset($params['email']) ? sanitize_email($params['email']) : '';

    if (empty($email) || !is_email($email)) {
        return new WP_Error('invalid_email', 'Invalid or missing email', array('status' => 400));
    }

    // Hook para capturar la cookie de sesión iniciada en $_COOKIE para que
    // wp_get_session_token() funcione y wp_create_nonce('wp_rest')
    // genere un nonce vinculado al token de sesión correcto.
    add_action('set_logged_in_cookie', function ($logged_in_cookie) {
        $_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
    });

    $user = get_user_by('email', $email);

    if ($user) {
        // CASO A: usuario existe
        $password = isset($params['password']) ? $params['password'] : '';
        if (empty($password)) {
            return new WP_Error('missing_password', 'Password is required', array('status' => 400));
        }

        // Máximo 5 intentos fallidos cada 15 minutos, de esta forma tenemos más seguridad
        $email_key = 'booking_auth_email_' . md5(strtolower($email));
        $email_attempts = (int) get_transient($email_key);

        if ($email_attempts >= 5) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Too many failed attempts for this email. Please try again later.'
            ], 429);
        }

        $creds = array(
            'user_login'    => $user->user_login,
            'user_password' => $password,
            'remember'      => true,
        );

        $user_signon = wp_signon($creds, false);

        if (is_wp_error($user_signon)) {
            // Incrementar el contador de intentos fallidos para este correo electrónico.
            set_transient($email_key, $email_attempts + 1, 15 * MINUTE_IN_SECONDS);
            return new WP_Error('invalid_credentials', 'Invalid credentials', array('status' => 401));
        }

        // Cuando se inicia sesión se hace el borrado del contador de fuerza bruta de correo electrónico
        delete_transient($email_key);

        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);

        return rest_ensure_response(array(
            'success'   => true,
            'nonce'     => wp_create_nonce('wp_rest'),
            'ajaxNonce' => wp_create_nonce('bm_pay_nonce'),
            'user'      => array(
                'name'  => $user->display_name,
                'email' => $user->user_email,
            ),
        ));
    } else {
        // CASO B: usuario NO existe
        $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';
        if (empty($name)) {
            return new WP_Error('missing_name', 'Name is required for new users', array('status' => 400));
        }

        $username = sanitize_user(explode('@', $email)[0]);
        // Evite nombres de usuario duplicados si existe el mismo prefijo.
        if (username_exists($username)) {
            $username = $username . '_' . wp_rand(1000, 9999);
        }

        $password = wp_generate_password();

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Establecer el nombre para mostrar
        wp_update_user(array(
            'ID'           => $user_id,
            'display_name' => $name,
            'first_name'   => $name,
        ));

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Notificación opcional
        wp_new_user_notification($user_id, null, 'user');

        return rest_ensure_response(array(
            'success'   => true,
            'created'   => true,
            'nonce'     => wp_create_nonce('wp_rest'),
            'ajaxNonce' => wp_create_nonce('bm_pay_nonce'),
            'user'      => array(
                'name'  => $name,
                'email' => $email,
            ),
        ));
    }
}
