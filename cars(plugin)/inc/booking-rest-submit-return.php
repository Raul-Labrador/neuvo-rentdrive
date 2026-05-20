<?php
/**
 * Booking REST API — Submit Return Proxy (POST)
 * Registers: POST /wp-json/booking/v1/submit-return
 * Reenvía a Laravel POST /api/returns
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_submit_return_route() {
    register_rest_route('booking/v1', '/submit-return', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_submit_return',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
}
add_action('rest_api_init', 'booking_register_submit_return_route');

function booking_handle_submit_return( WP_REST_Request $request ) {
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
    $km_returned    = absint( $request->get_param('km_returned') );
    $is_clean       = (bool) $request->get_param('is_clean');
    $notes          = sanitize_textarea_field( $request->get_param('notes') ?: '' );
    $damages        = sanitize_textarea_field( $request->get_param('damages') ?: '' );

    if ( empty($reservation_id) || empty($km_returned) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Faltan campos obligatorios (reservation_id, km_returned).',
        ), 400);
    }

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Llamar a Laravel (POST)
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/returns';

    $response = wp_remote_post($laravel_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ),
        'body'    => wp_json_encode(array(
            'reservation_id' => $reservation_id,
            'km_returned'    => $km_returned,
            'is_clean'       => $is_clean,
            'notes'          => $notes,
            'damages'        => $damages,
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
        ), 500);
    }

    return new WP_REST_Response($data, $status_code);
}
