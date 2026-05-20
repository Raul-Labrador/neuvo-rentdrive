<?php
/**
 * Punto final de la API REST de reservas
 * Registros: POST /wp-json/booking/v1/reserve
 * Valida los datos, los envía a Laravel y devuelve la respuesta.
 */

defined('ABSPATH') or die('No direct access.');

/**
 * Registra la ruta de reserva REST
 */
function booking_register_rest_route()
{
    register_rest_route('booking/v1', '/reserve', array(
        'methods' => 'POST',
        'callback' => 'booking_handle_reserve',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'booking_register_rest_route');

// Gestionar la solicitud de reserva.
function booking_handle_reserve(WP_REST_Request $request)
{
    // Verificar nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Nonce de seguridad inválido.',
        ), 403);
    }

    // Verificar que el usuario haya iniciado sesión.
    if (!is_user_logged_in()) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Debes iniciar sesión para reservar.',
        ), 401);
    }

    // Obtener y sanear datos
    $car_id = absint($request->get_param('car_id'));
    $start_date = sanitize_text_field($request->get_param('start_date'));
    $end_date = sanitize_text_field($request->get_param('end_date'));

    // Obtener datos de usuario
    $current_user = wp_get_current_user();
    $customer_name = $current_user->display_name;
    $customer_email = $current_user->user_email;
    $wp_user_id = $current_user->ID;

    // Validar campos obligatorios
    if (empty($car_id) || empty($start_date) || empty($end_date)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Todos los campos son obligatorios.',
        ), 400);
    }

    // Validar fechas
    $start_ts = strtotime($start_date);
    $end_ts = strtotime($end_date);

    if ($start_ts === false || $end_ts === false) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Las fechas no tienen un formato válido.',
        ), 400);
    }

    if ($start_ts >= $end_ts) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'La fecha de inicio debe ser anterior a la de fin.',
        ), 400);
    }

    if ($start_ts < strtotime('today')) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'La fecha de inicio no puede ser en el pasado.',
        ), 400);
    }

    // Verificar constante de API
    if (!defined('RENTWAY_LARAVEL_API_BASE')) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    // Pasar a Laravel
    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/reservations';

    $response = wp_remote_post($laravel_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ),
        'body' => wp_json_encode(array(
            'car_id' => $car_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'wp_user_id' => $wp_user_id,
            'payment_status' => 'paid',
            'stripe_id' => sanitize_text_field($request->get_param('payment_intent_id')),
        )),
        'timeout' => 15,
    ));

    // Manejar errores de conexión
    if (is_wp_error($response)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Error de conexión con el servidor: ' . $response->get_error_message(),
        ), 502);
    }

    // Analizar la respuesta de Laravel
    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Respuesta inválida del servidor.',
        ), 502);
    }

    // Devuelve la respuesta de Laravel al frontend.
    return new WP_REST_Response($data, $status_code);
}
