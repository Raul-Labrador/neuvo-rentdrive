<?php
defined('ABSPATH') or die('No script kiddies please!');

if (!function_exists('booking_get_client_ip')) {
    function booking_get_client_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

add_action('rest_api_init', function () {
    register_rest_route('booking/v1', '/check-user', array(
        'methods'             => 'POST',
        'callback'            => 'rlp_booking_rest_check_user',
        'permission_callback' => '__return_true',
    ));
});

function rlp_booking_rest_check_user($request) {
    $nonce = $request->get_header('X-WP-Nonce');

    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid security token.'
        ], 403);
    }

    // Máximo 20 solicitudes cada 10 minutos por IP.
    $ip = booking_get_client_ip();
    $key = 'booking_check_user_' . md5($ip);
    $count = (int) get_transient($key);

    if ($count >= 20) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Too many requests. Please try again later.'
        ], 429);
    }

    set_transient($key, $count + 1, 10 * MINUTE_IN_SECONDS);

    $params = $request->get_json_params();
    $email  = isset($params['email']) ? sanitize_email($params['email']) : '';

    if (empty($email) || !is_email($email)) {
        return new WP_Error('invalid_email', 'Invalid or missing email', array('status' => 400));
    }

    $user = get_user_by('email', $email);

    return rest_ensure_response(array(
        'exists' => $user ? true : false,
    ));
}
