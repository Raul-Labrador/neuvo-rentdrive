<?php
/**
 * Booking REST API — User Reservations Proxy (POST)
 * Registro: POST /wp-json/booking/v1/my-reservations
 * Reenvía a Laravel GET /api/reservations?wp_user_id={id}
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_user_reservations_route() {
    register_rest_route('booking/v1', '/my-reservations', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_user_reservations',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
}
add_action('rest_api_init', 'booking_register_user_reservations_route');

function booking_handle_user_reservations( WP_REST_Request $request ) {
    // Verificar nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Sesión caducada o inválida.',
        ), 403);
    }

    // Obtener wp_user_id del usuario logueado
    $wp_user_id = get_current_user_id();

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Llamar a Laravel (GET)
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/reservations?wp_user_id=' . $wp_user_id;

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

    // Parsear respuesta
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Respuesta inválida del backend.',
        ), 500);
    }

    // Forward exacto de la respuesta de Laravel
    $status_code = wp_remote_retrieve_response_code($response);
    return new WP_REST_Response($data, $status_code);
}
