<?php
/**
 * Booking REST API — Booked Dates Proxy (POST)
 * Regista: POST /wp-json/booking/v1/booked-dates
 * Reenvía a Laravel GET /api/cars/{id}/booked-dates
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_booked_dates_route() {
    register_rest_route('booking/v1', '/booked-dates', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_booked_dates',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'booking_register_booked_dates_route');

function booking_handle_booked_dates( WP_REST_Request $request ) {
    // No se requiere verificación de nonce: este endpoint devuelve datos públicos de disponibilidad de vehículos.
    // Los usuarios anónimos no pueden generar un nonce wp_rest válido, y estos datos son
    // de solo lectura y sin efectos secundarios.

    $car_id = absint( $request->get_param('car_id') );
    if ( empty($car_id) ) {
        return new WP_REST_Response(array(), 400);
    }

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(), 500);
    }

    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/cars/' . $car_id . '/booked-dates';

    $response = wp_remote_get($laravel_url, array(
        'headers' => array(
            'Accept' => 'application/json',
        ),
        'timeout' => 15,
    ));

    if ( is_wp_error($response) ) {
        error_log("BOOKED-DATES PROXY ERROR: " . $response->get_error_message());
        return new WP_REST_Response(array(), 502);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ( json_last_error() !== JSON_ERROR_NONE || ! is_array($data) ) {
        return new WP_REST_Response(array(), 502);
    }

    return new WP_REST_Response($data, 200);
}
