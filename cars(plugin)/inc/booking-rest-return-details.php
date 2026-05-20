<?php
/**
 * Booking REST API — Return Details Proxy (POST)
 * Registro: POST /wp-json/booking/v1/return-details
 * Reenvía a Laravel GET /api/returns/{reservation_id}
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_return_details_route() {
    register_rest_route('booking/v1', '/return-details', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_return_details',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
}
add_action('rest_api_init', 'booking_register_return_details_route');

function booking_handle_return_details( WP_REST_Request $request ) {
    // Verificar nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Sesión caducada o inválida.',
        ), 403);
    }

    // Obtener reservation_id
    $reservation_id = absint( $request->get_param('reservation_id') );
    if ( empty($reservation_id) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Falta reservation_id.',
        ), 400);
    }

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Llamar a Laravel (GET)
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/returns/' . $reservation_id;

    $response = wp_remote_get($laravel_url, array(
        'headers' => array(
            'Accept' => 'application/json',
        ),
        'timeout' => 15,
    ));

    // Error de conexión
    if ( is_wp_error($response) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Error de conexión con el backend.',
        ), 502);
    }

    // Forward respuesta Laravel
    $status_code = wp_remote_retrieve_response_code($response);
    $body        = wp_remote_retrieve_body($response);
    $data        = json_decode($body, true);

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Respuesta inválida del backend.',
        ), 500);
    }

    return new WP_REST_Response($data, $status_code);
}
