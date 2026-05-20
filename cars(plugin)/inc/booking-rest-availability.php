<?php
/**
 * API REST de reservas: proxy de verificación de disponibilidad (POST)
 * Registros: POST /wp-json/booking/v1/availability
 * Valida la sesión/nonce, reenvía a Laravel y devuelve la respuesta.
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_availability_route() {
    register_rest_route('booking/v1', '/availability', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_availability',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'booking_register_availability_route');

function booking_handle_availability( WP_REST_Request $request ) {
    // Verificar el nonce: esto también autentica al usuario a partir del propio nonce,
    // lo cual es fundamental para el flujo de autenticación del Paso 0, donde la cookie de autenticación puede
    // no estar aún disponible para las llamadas posteriores a fetch().
    $nonce = $request->get_header('X-WP-Nonce');
    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response(array(
            'available' => false,
            'message'   => 'Sesión caducada o inválida.',
        ), 403);
    }

    // Obtener y sanitizar parámetros
    $car_id     = absint( $request->get_param('car_id') );
    $start_date = sanitize_text_field( $request->get_param('start_date') );
    $end_date   = sanitize_text_field( $request->get_param('end_date') );

    if ( empty($car_id) || empty($start_date) || empty($end_date) ) {
        return new WP_REST_Response(array(
            'available' => false,
            'message'   => 'Faltan parámetros requeridos.',
        ), 400);
    }

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'available' => false,
            'message'   => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Llamar a Laravel
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/availability';

    $response = wp_remote_post($laravel_url, array(
        'headers' => array(
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode(array(
            'car_id'     => $car_id,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        )),
        'timeout' => 15,
    ));

    // Error de conexión
    if ( is_wp_error($response) ) {
        return new WP_REST_Response(array(
            'available' => false,
            'message'   => 'Error de conexión con el backend.',
        ), 502);
    }

    // Parsear respuesta con validación
    $status_code = wp_remote_retrieve_response_code($response);
    $body        = wp_remote_retrieve_body($response);
    $data        = json_decode($body, true);

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_REST_Response(array(
            'available' => false,
            'message'   => 'Respuesta inválida del backend',
        ), 502);
    }

    return new WP_REST_Response($data, $status_code);
}
