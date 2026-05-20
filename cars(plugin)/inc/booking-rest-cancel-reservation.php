<?php
/**
 * Booking REST API — Cancel Reservation Proxy (POST)
 * Registra: POST /wp-json/booking/v1/cancel-reservation
 * Reenvía a Laravel PATCH /api/reservations/{id}/cancel
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_cancel_reservation_route() {
    register_rest_route('booking/v1', '/cancel-reservation', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_cancel_reservation',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
}
add_action('rest_api_init', 'booking_register_cancel_reservation_route');

function booking_handle_cancel_reservation( WP_REST_Request $request ) {
    // Verificar nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Sesión caducada o inválida.',
        ), 403);
    }

    // Obtener datos
    $reservation_id = absint( $request->get_param('reservation_id') );
    if ( empty($reservation_id) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Falta reservation_id.',
        ), 400);
    }

    $current_user = wp_get_current_user();
    $wp_user_id   = $current_user->ID;

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Llamar a Laravel (PATCH)
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/reservations/' . $reservation_id . '/cancel';

    $response = wp_remote_request($laravel_url, array(
        'method'  => 'PATCH',
        'headers' => array(
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ),
        'body'    => wp_json_encode(array(
            'wp_user_id' => $wp_user_id,
        )),
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
        ), 502);
    }

    return new WP_REST_Response($data, $status_code);
}
