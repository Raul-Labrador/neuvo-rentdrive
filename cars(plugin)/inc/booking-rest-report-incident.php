<?php
/**
 * API REST de reservas: Proxy para reportar incidentes (POST)
 * Registros: POST /wp-json/booking/v1/report-incident
 * Reenvío a Laravel POST /api/incidents
 * Admite tanto JSON (sin imágenes) como multipart/form-data (con imágenes).
 */

defined('ABSPATH') or die('No direct access.');

function booking_register_report_incident_route() {
    register_rest_route('booking/v1', '/report-incident', array(
        'methods'  => 'POST',
        'callback' => 'booking_handle_report_incident',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
}
add_action('rest_api_init', 'booking_register_report_incident_route');

function booking_handle_report_incident( WP_REST_Request $request ) {
    // Verificar nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Sesión caducada o inválida.',
        ), 403);
    }

    // Obtener usuario logueado
    $wp_user_id = get_current_user_id();
    if ( ! $wp_user_id ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Usuario no autenticado.',
        ), 401);
    }

    // Obtener datos del body
    $reservation_id = absint( $request->get_param('reservation_id') );
    $type           = sanitize_text_field( $request->get_param('type') ?: '' );
    $description    = sanitize_textarea_field( $request->get_param('description') ?: '' );

    if ( empty($reservation_id) || empty($type) || empty($description) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Faltan campos obligatorios (reservation_id, type, description).',
        ), 400);
    }

    // Detectar si hay imágenes
    $has_images = ! empty($_FILES['images']) && ! empty($_FILES['images']['name'][0]);

    // Verificar constante de API
    if ( ! defined('RENTWAY_LARAVEL_API_BASE') ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Laravel API base URL no configurada.',
        ), 500);
    }

    $laravel_url = RENTWAY_LARAVEL_API_BASE . '/incidents';

    if ( $has_images ) {
        // Construir límite y body manualmente
        $boundary = wp_generate_password(24, false);

        $body = '';

        // Campos de texto
        $fields = array(
            'reservation_id' => $reservation_id,
            'wp_user_id'     => $wp_user_id,
            'type'           => $type,
            'description'    => $description,
        );

        foreach ( $fields as $name => $value ) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
            $body .= "{$value}\r\n";
        }

        // Archivos de imagen
        $file_count = count($_FILES['images']['name']);
        for ( $i = 0; $i < $file_count; $i++ ) {
            if ( $_FILES['images']['error'][$i] !== UPLOAD_ERR_OK ) {
                continue;
            }

            $file_name = sanitize_file_name($_FILES['images']['name'][$i]);
            $file_type = $_FILES['images']['type'][$i];
            $file_data = file_get_contents($_FILES['images']['tmp_name'][$i]);

            // Validar tipo MIME
            $allowed_mimes = array('image/jpeg', 'image/png', 'image/jpg', 'image/webp');
            if ( ! in_array($file_type, $allowed_mimes) ) {
                continue;
            }

            // Validar tamaño (5MB max)
            if ( $_FILES['images']['size'][$i] > 5242880 ) {
                continue;
            }

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"images[]\"; filename=\"{$file_name}\"\r\n";
            $body .= "Content-Type: {$file_type}\r\n\r\n";
            $body .= $file_data . "\r\n";
        }

        $body .= "--{$boundary}--\r\n";

        $response = wp_remote_post($laravel_url, array(
            'headers' => array(
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                'Accept'       => 'application/json',
            ),
            'body'    => $body,
            'timeout' => 30,
        ));

    } else {
        // JSON (sin imágenes) — comportamiento original
        $response = wp_remote_post($laravel_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ),
            'body'    => wp_json_encode(array(
                'reservation_id' => $reservation_id,
                'wp_user_id'     => $wp_user_id,
                'type'           => $type,
                'description'    => $description,
            )),
            'timeout' => 15,
        ));
    }

    // Error de conexión
    if ( is_wp_error($response) ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Error de conexión con el backend.',
        ), 502);
    }

    // Respuesta Laravel
    $status_code = wp_remote_retrieve_response_code($response);
    $body_resp   = wp_remote_retrieve_body($response);
    $data        = json_decode($body_resp, true);

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Respuesta inválida del backend.',
        ), 500);
    }

    return new WP_REST_Response($data, $status_code);
}
