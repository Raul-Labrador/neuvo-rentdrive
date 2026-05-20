<?php

if (!function_exists('alquilercocheswp_setup')):
    // Configuración básica del tema.
    function alquilercocheswp_setup()
    {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        
        // Se carga el dominio de traducciones
        load_theme_textdomain('alquilercocheswp', get_template_directory() . '/languages');
    }
endif;
add_action('after_setup_theme', 'alquilercocheswp_setup');

/**
 * Carga de scripts y estilos principales.
 */
function alquilercocheswp_scripts() {
    // Fuentes de Google y utilidades de Bootstrap
    wp_enqueue_style('alquilercocheswp-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap', array(), null);
    wp_enqueue_style('alquilercocheswp-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2');
    wp_enqueue_style('alquilercocheswp-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css', array(), '1.11.1');
    
    // Estilos propios del tema
    wp_enqueue_style('alquilercocheswp-style', get_stylesheet_uri(), array(), '1.0.2');
    wp_enqueue_style('cars-details', get_template_directory_uri() . '/assets/css/cars-details.css');
    
    // Scripts globales
    wp_enqueue_script('alquilercocheswp-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
    wp_enqueue_script('alquilercocheswp-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true);
    wp_enqueue_script('alquilercocheswp-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array('alquilercocheswp-gsap'), '3.12.5', true);
    wp_enqueue_script('alquilercocheswp-scrollto', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js', array('alquilercocheswp-gsap'), '3.12.5', true);
    wp_enqueue_script('alquilercocheswp-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery', 'alquilercocheswp-gsap'), '1.0.1', true);

    // Variables AJAX disponibles para el frontend (JavaScript)
    wp_localize_script('alquilercocheswp-main', 'bmAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bm_pay_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'alquilercocheswp_scripts');

// Carga JS propio de la página de reservations
function neuvo_cargar_script_reservas() {
    if (is_page('my-reservations')) {
        
        wp_enqueue_script(
            'neuvo-reservations-js', 
            get_template_directory_uri() . '/assets/js/reservations.js',
            array(), 
            '1.1.0', 
            true
        );

        wp_localize_script(
            'neuvo-reservations-js', 
            'neuvoData', 
            array(
                'nonce' => wp_create_nonce('wp_rest')
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'neuvo_cargar_script_reservas');

// Carga Js propio de la página de terms
function neuvo_enqueue_terms_scripts() {
    if (is_page('terms')) { 
        wp_enqueue_script(
            'neuvo-terms-js',
            get_template_directory_uri() . '/js/terms.js',
            array(),
            '1.0', 
            true 
        );
    }
}
add_action('wp_enqueue_scripts', 'neuvo_enqueue_terms_scripts');

/**
 * AUTENTICACIÓN Y REDIRECCIONES (LOGIN / REGISTRO)
 */

/**
 * Redirige la ruta por defecto wp-login.php hacia nuestras páginas personalizadas.
 */
function custom_login_redirect() {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    if (isset($_GET['key'])) {
        $action = 'resetpass';
    }

    // 1. Interceptar el Reset Password SIEMPRE (incluso si está logueado por accidente)
    if (in_array($action, array('rp', 'resetpass'), true)) {
        $key   = isset($_GET['key'])   ? $_GET['key']   : '';
        $login = isset($_GET['login']) ? $_GET['login'] : '';

        // Si WP ya procesó la URL y quitó la key, la intentamos recuperar de su cookie
        $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
        if (empty($key) && isset($_COOKIE[$rp_cookie])) {
            $cookie_val = wp_unslash($_COOKIE[$rp_cookie]);
            if (strpos($cookie_val, ':') !== false) {
                list($cookie_login, $cookie_key) = explode(':', $cookie_val, 2);
                $key   = $cookie_key;
                $login = $cookie_login;
            }
        }

        $target = home_url('/lost-password');
        if (!empty($key) && !empty($login)) {
            $target = add_query_arg(array('key' => $key, 'login' => $login), $target);
        }
        
        wp_redirect($target);
        exit;
    }

    // Para el resto de acciones, si está logueado no hacemos nada
    if (is_user_logged_in()) return;

    // El logout lo dejamos pasar para que WP lo procese normalmente
    if ($action === 'logout') return;

    if (isset($_GET['checkemail'])) {
        wp_redirect(add_query_arg('registered', '1', home_url('/login')));
        exit;
    }

    $redirect_to = isset($_GET['redirect_to']) ? wp_unslash($_GET['redirect_to']) : '';
    $redirect_to = wp_validate_redirect($redirect_to, '');

    if ($action === 'register') {
        $target = home_url('/register');
    } elseif ($action === 'lostpassword') {
        $target = home_url('/lost-password');
    } else {
        $target = home_url('/login');
    }
    
    if ($redirect_to) {
        $target = add_query_arg('redirect_to', rawurlencode($redirect_to), $target);
    }

    wp_redirect($target);
    exit;
}
add_action('login_init', 'custom_login_redirect');

/**
 * Fuerza que el registro de usuarios esté habilitado en todo momento.
 */
function alquilercocheswp_enable_registration() {
    if (!get_option('users_can_register')) {
        update_option('users_can_register', 1);
    }
}
add_action('init', 'alquilercocheswp_enable_registration');

/**
 * Obtiene la URL segura a donde se redirigirá tras el login exitoso.
 */
function alquilercocheswp_get_login_redirect() {
    $redirect_to = isset($_REQUEST['redirect_to']) ? wp_unslash($_REQUEST['redirect_to']) : '';
    return wp_validate_redirect($redirect_to, home_url('/'));
}

/**
 * Renderiza el botón interactivo del perfil de usuario y dropdown en la barra de navegación.
 */
function alquilercocheswp_login_cta() {
    if (is_user_logged_in()) {
        $profile_url  = admin_url('profile.php');
        $current_user = wp_get_current_user();
        ?>
        <div class="dropdown d-inline-block">
            <a class="dropdown-toggle d-flex align-items-center gap-2 text-decoration-none"
               href="#" role="button" id="userMenuLink" data-bs-toggle="dropdown" aria-expanded="false"
               style="background:transparent; border:1px solid #7b7b7bff; border-radius:6px; padding:8px 16px; color:#ffffff; transition:0.3s ease;">
                <span class="d-none d-md-block text-white small m-0 text-uppercase"
                      style="font-family:'Montserrat', sans-serif; font-weight:500; letter-spacing:0.05em; font-size: 0.85rem;"><?php echo esc_html($current_user->display_name); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border animate-dropdown"
                aria-labelledby="userMenuLink"
                style="background-color:#0a0a0a; border-color:rgba(255,255,255,0.1) !important; border-radius:4px; margin-top:12px; min-width:220px; font-family:'Montserrat', sans-serif;">
                <li class="px-3 py-3 border-bottom mb-2" style="border-color:rgba(255,255,255,0.1) !important;">
                    <span class="d-block" style="font-size:0.75rem; color:#a3a3a3; text-transform:uppercase; letter-spacing:0.1em;">Welcome,</span>
                    <span class="d-block text-white mt-1" style="font-family:'Doxent', sans-serif; font-size:1.1rem; letter-spacing:0.05em; text-transform:uppercase;"><?php echo esc_html($current_user->display_name); ?></span>
                </li>
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo esc_url($profile_url); ?>" style="font-size:0.85rem; letter-spacing:0.05em; color:#d4d4d4;"><i class="bi bi-person-circle" style="color:#ffffff;"></i> My Profile</a></li>
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo esc_url(home_url('/my-reservations')); ?>" style="font-size:0.85rem; letter-spacing:0.05em; color:#d4d4d4;"><i class="bi bi-car-front" style="color:#ffffff;"></i> My Reservations</a></li>
                <?php if (current_user_can('administrator')): ?>
                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo esc_url(alquilercocheswp_get_laravel_dashboard_url(wp_get_current_user())); ?>" target="_blank" style="font-size:0.85rem; letter-spacing:0.05em; color:#d4d4d4;"><i class="bi bi-speedometer2" style="color:#ffffff;"></i> Dashboard</a></li>
                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo esc_url(admin_url()); ?>" style="font-size:0.85rem; letter-spacing:0.05em; color:#d4d4d4;"><i class="bi bi-wordpress" style="color:#ffffff;"></i> WordPress Admin</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider my-2" style="border-color:rgba(255,255,255,0.1);"></li>
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="<?php echo esc_url(wp_logout_url(home_url())); ?>" style="font-size:0.85rem; letter-spacing:0.05em;"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
            </ul>
        </div>
        <?php
    } else {
        $redirect_to = alquilercocheswp_get_login_redirect();
        if (empty($redirect_to) || $redirect_to === home_url('/')) {
            $redirect_to = home_url($_SERVER['REQUEST_URI']);
        }
        $login_url = add_query_arg('redirect_to', rawurlencode($redirect_to), home_url('/login'));
        ?>
        <a class="btn-login-icon" href="<?php echo esc_url($login_url); ?>" title="Login">
            <span>Login</span>
        </a>
        <?php
    }
}

/**
 * Filtro que asigna la plantilla personalizada dependiendo de la petición de login/register.
 */
function alquilercocheswp_force_login_register_page_template($template) {
    if (is_page('login')) {
        $custom = get_template_directory() . '/page-login.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('register')) {
        $custom = get_template_directory() . '/page-register.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('lost-password')) {
        $custom = get_template_directory() . '/page-lost-password.php';
        if (file_exists($custom)) return $custom;
    }
    return $template;
}
add_filter('page_template', 'alquilercocheswp_force_login_register_page_template');

/**
 * Reemplaza la URL del sistema de WordPress por la URL del diseño personalizado de Login.
 */
function custom_login_url($login_url, $redirect, $force_reauth) {
    $login_page = home_url('/login');
    if (!empty($redirect)) {
        $safe_redirect = wp_validate_redirect($redirect, home_url('/'));
        if ($safe_redirect) {
            $login_page = add_query_arg('redirect_to', $safe_redirect, $login_page);
        }
    }
    return $login_page;
}
add_filter('login_url', 'custom_login_url', 10, 3);

/**
 * Reemplaza la URL del sistema de WordPress por la URL del diseño personalizado de Registro.
 */
function custom_register_url($register_url) {
    return home_url('/register');
}
add_filter('register_url', 'custom_register_url');

/**
 * Carga directamente la plantilla si la URL contiene las rutas `/login` o `/register`.
 */
function alquilercocheswp_force_custom_login_register_template() {
    if (is_admin()) return;

    $home_path     = untrailingslashit(parse_url(home_url(), PHP_URL_PATH));
    $request_path  = untrailingslashit(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $relative_path = ltrim(substr($request_path, strlen($home_path)), '/');

    if (in_array($relative_path, array('login', 'register', 'lost-password'), true)) {
        include get_template_directory() . '/page-' . $relative_path . '.php';
        exit;
    }
}
add_action('template_redirect', 'alquilercocheswp_force_custom_login_register_template', 5);

/**
 * Hace que todos los enlaces de "Forgot password" de WordPress apunten
 * a nuestra página personalizada /lost-password.
 */
function custom_lostpassword_url($url) {
    return home_url('/lost-password');
}
add_filter('lostpassword_url', 'custom_lostpassword_url', 10, 0);

/**
 * Desactiva la barra de administración en el frontend para usuarios recién registrados.
 */
function alquilercocheswp_desactivar_admin_bar_nuevos_usuarios($user_id) {
    update_user_meta($user_id, 'show_admin_bar_front', 'false');
}
add_action('user_register', 'alquilercocheswp_desactivar_admin_bar_nuevos_usuarios');

// Desactiva la barra de administración de WordPress globalmente en el frontend
add_filter('show_admin_bar', '__return_false');


/**
 * INTEGRACIÓN SINGLE SIGN-ON (SSO) CON LARAVEL
 */

/**
 * Genera el token SSO codificado con HMAC basado en un secreto compartido.
 */
function alquilercocheswp_generate_sso_token($email) {
    $secret    = defined('LARAVEL_SSO_SECRET') ? LARAVEL_SSO_SECRET : wp_salt('auth');
    $timestamp = time();
    $payload   = $email . '|' . $timestamp;
    $signature = hash_hmac('sha256', $payload, $secret);
    
    return base64_encode($email . '|' . $timestamp . '|' . $signature);
}

/**
 * Obtiene la URL completa al dashboard de Laravel (incluyendo el token SSO generado).
 */
function alquilercocheswp_get_laravel_dashboard_url($user) {
    if (!current_user_can('administrator') && !user_can($user, 'administrator')) {
        return 'https://neuvo-app.com/';
    }
    $token = alquilercocheswp_generate_sso_token($user->user_email);
    return 'https://admin.neuvo-app.com/wp-sso?token=' . urlencode($token);
}

/**
 * Filtra la redirección de los administradores tras el inicio de sesión.
 */
function alquilercocheswp_admin_login_redirect($redirect_to, $request, $user) {
    if (is_wp_error($user)) return $redirect_to;
    if (isset($user->roles) && in_array('administrator', $user->roles, true)) {
        return alquilercocheswp_get_laravel_dashboard_url($user);
    }
    return $redirect_to;
}
add_filter('login_redirect', 'alquilercocheswp_admin_login_redirect', 10, 3);

/**
 * Añade el enlace rápido al Panel de Laravel desde el menú administrativo de WordPress.
 */
function alquilercocheswp_add_laravel_admin_link($wp_admin_bar) {
    if (current_user_can('administrator')) {
        $wp_admin_bar->add_node(array(
            'id'    => 'laravel_admin_panel',
            'title' => '<span class="ab-icon dashicons dashicons-dashboard"></span> <span class="ab-label">Panel Administración</span>',
            'href'  => alquilercocheswp_get_laravel_dashboard_url(wp_get_current_user()),
            'meta'  => array('target' => '_blank'),
        ));
    }
}
add_action('admin_bar_menu', 'alquilercocheswp_add_laravel_admin_link', 999);

/**
 * Procesa la acción de logout desde el entorno de SSO (Laravel a WordPress).
 */
function alquilercocheswp_handle_sso_logout() {
    $home_path    = untrailingslashit(parse_url(home_url(), PHP_URL_PATH));
    $request_path = untrailingslashit(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $relative     = ltrim(substr($request_path, strlen($home_path)), '/');

    if ($relative !== 'wp-logout-sso') return;

    $raw_token = isset($_GET['token']) ? $_GET['token'] : '';
    if (empty($raw_token)) { wp_redirect(home_url('/')); exit; }

    $decoded = base64_decode(str_replace(' ', '+', $raw_token), true);
    if ($decoded === false) { wp_redirect(home_url('/')); exit; }

    $parts = explode('|', $decoded);
    if (count($parts) !== 3 || $parts[0] !== 'logout') { wp_redirect(home_url('/')); exit; }

    [, $timestamp, $signature] = $parts;
    
    // Evita la repetición/ataques si pasaron más de 120s
    if ((time() - (int) $timestamp) > 120) { wp_redirect(home_url('/')); exit; }

    $secret   = defined('LARAVEL_SSO_SECRET') ? LARAVEL_SSO_SECRET : wp_salt('auth');
    $expected = hash_hmac('sha256', 'logout|' . $timestamp, $secret);
    
    if (!hash_equals($expected, $signature)) { wp_redirect(home_url('/')); exit; }

    if (is_user_logged_in()) wp_logout();
    wp_redirect(home_url('/'));
    exit;
}
add_action('template_redirect', 'alquilercocheswp_handle_sso_logout', 1);

/**
 * Al cerrar sesión en WordPress, redirige para que cierre también en Laravel.
 */
add_filter('logout_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    return 'https://admin.neuvo-app.com/wp-sso-logout';
}, 10, 3);

// Permite redirigir externamente al dominio de la aplicación de Laravel
add_filter('allowed_redirect_hosts', function ($hosts) {
    $hosts[] = 'https://admin.neuvo-app.com/';
    return $hosts;
});


/**
 * CONFIGURACIÓN DE COMENTARIOS (Desactivados Globalmente)
 */
add_filter('comments_open',  '__return_false', 20, 2);
add_filter('pings_open',     '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

add_action('init', function () {
    foreach (get_post_types() as $type) {
        if (post_type_supports($type, 'comments')) {
            remove_post_type_support($type, 'comments');
            remove_post_type_support($type, 'trackbacks');
        }
    }
});
add_action('admin_menu',     function () { remove_menu_page('edit-comments.php'); });
add_action('admin_bar_menu', function ($bar) { $bar->remove_menu('comments'); }, 999);


/**
 * INTEGRACIÓN CON STRIPE Y GUARDADO DE RESERVAS
 * 
 * Funciones para gestionar el pago con Stripe en la capa de frontend. 
 * Implementadas de forma nativa con wp_remote_request (sin SDK externos).
 */

/**
 * Realiza llamadas HTTP directamente a la API REST de Stripe.
 *
 * @param  string $endpoint Endpoint de Stripe (ej: 'payment_intents').
 * @param  array  $body     Cuerpo con los parámetros en formato x-www-form-urlencoded.
 * @param  string $method   Método de la solicitud ('POST' o 'GET').
 * @return array|WP_Error   Array decodificado JSON o error de WordPress.
 */
function bm_stripe_request( $endpoint, $body = [], $method = 'POST' ) {
    if ( ! defined( 'STRIPE_SECRET_KEY' ) || empty( STRIPE_SECRET_KEY ) ) {
        return new WP_Error( 'no_key', 'STRIPE_SECRET_KEY no está definida en wp-config.php' );
    }

    $args = array(
        'method'  => $method,
        'timeout' => 30,
        'headers' => array(
            'Authorization'  => 'Bearer ' . STRIPE_SECRET_KEY,
            'Content-Type'   => 'application/x-www-form-urlencoded',
            'Stripe-Version' => '2023-10-16',
        ),
    );

    if ( ! empty( $body ) ) {
        $args['body'] = $body;
    }

    $response = wp_remote_request( 'https://api.stripe.com/v1/' . $endpoint, $args );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $decoded = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! empty( $decoded['error'] ) ) {
        return new WP_Error(
            $decoded['error']['type']    ?? 'stripe_error',
            $decoded['error']['message'] ?? 'Unknown Stripe error'
        );
    }

    return $decoded;
}

/**
 * Llamada AJAX (Frontend) - Genera un "Payment Intent" de Stripe.
 * Retorna el 'client_secret' para autorizar el cargo en el navegador del cliente.
 */
function bm_create_payment_intent() {
    if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'bm_pay_nonce' ) ) {
        wp_send_json_error( [ 'message' => 'Sesión expirada. Recarga la página.' ] );
    }

    $amount = intval( $_POST['amount'] ?? 0 );
    if ( $amount <= 0 ) {
        wp_send_json_error( [ 'message' => 'Importe inválido. Selecciona las fechas de nuevo.' ] );
    }

    $car_name   = sanitize_text_field( $_POST['car_name']   ?? 'Vehicle' );
    $start_date = sanitize_text_field( $_POST['start_date'] ?? '' );
    $end_date   = sanitize_text_field( $_POST['end_date']   ?? '' );
    $user       = wp_get_current_user();

    // Iniciar intento de pago
    $result = bm_stripe_request( 'payment_intents', array(
        'amount'                 => $amount,
        'currency'               => 'eur',
        'payment_method_types[]' => 'card',
        'description'            => "Reserva de $car_name ($start_date a $end_date)",
        'receipt_email'          => $user->user_email,
        'metadata[car_id]'       => sanitize_text_field( $_POST['car_id'] ?? '' ),
        'metadata[car_name]'     => $car_name,
        'metadata[user_id]'      => $user->ID,
        'metadata[user_email]'   => $user->user_email,
        'metadata[start_date]'   => $start_date,
        'metadata[end_date]'     => $end_date,
        'metadata[source]'       => 'booking_modal',
    ) );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( [ 'message' => $result->get_error_message() ] );
    }

    if ( empty( $result['client_secret'] ) ) {
        wp_send_json_error( [ 'message' => 'No se ha podido iniciar el pago. Revisa STRIPE_SECRET_KEY en el entorno.' ] );
    }

    wp_send_json_success( [ 'clientSecret' => $result['client_secret'] ] );
}
add_action( 'wp_ajax_bm_create_payment_intent',        'bm_create_payment_intent' );
add_action( 'wp_ajax_nopriv_bm_create_payment_intent', 'bm_create_payment_intent' );

/**
 * Llamada AJAX (Frontend) - Valida que el pago haya sido finalizado en Stripe y
 * guarda oficialmente la reserva como un "Custom Post Type" (booking).
 */
function bm_save_booking() {
    if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'bm_pay_nonce' ) ) {
        wp_send_json_error( [ 'message' => 'Sesión expirada.' ] );
    }

    $payment_intent_id = sanitize_text_field( $_POST['payment_intent_id'] ?? '' );
    $car_id            = intval( $_POST['car_id'] ?? 0 );
    $start_date        = sanitize_text_field( $_POST['start_date'] ?? '' );
    $end_date          = sanitize_text_field( $_POST['end_date'] ?? '' );
    $notes             = sanitize_textarea_field( $_POST['notes'] ?? '' );

    if ( empty( $payment_intent_id ) ) {
        wp_send_json_error( [ 'message' => 'No se ha proporcionado el identificador del pago.' ] );
    }

    // Confirmación en servidor: verificamos de nuevo en la API de Stripe
    // para evitar que usuarios manipulen el estado en frontend.
    $intent = bm_stripe_request( 'payment_intents/' . $payment_intent_id, [], 'GET' );

    if ( is_wp_error( $intent ) ) {
        wp_send_json_error( [ 'message' => $intent->get_error_message() ] );
    }

    if ( ( $intent['status'] ?? '' ) !== 'succeeded' ) {
        wp_send_json_error( [ 'message' => 'El pago no ha sido completado. (Estado: ' . ( $intent['status'] ?? 'desconocido' ) . ')' ] );
    }

    // Registro seguro de la reserva en base de datos.
    $booking_id = wp_insert_post( [
        'post_type'   => 'booking',
        'post_status' => 'publish',
        'post_title'  => 'Reserva #' . $payment_intent_id,
        'meta_input'  => [
            '_car_id'            => $car_id,
            '_user_id'           => get_current_user_id(),
            '_start_date'        => $start_date,
            '_end_date'          => $end_date,
            '_notes'             => $notes,
            '_payment_intent_id' => $payment_intent_id,
            '_amount_paid'       => $intent['amount']   ?? 0,
            '_currency'          => $intent['currency'] ?? 'eur',
            '_booking_status'    => 'confirmed',
        ],
    ] );

    if ( is_wp_error( $booking_id ) ) {
        wp_send_json_error( [ 'message' => 'No se pudo guardar la reserva en el sistema: ' . $booking_id->get_error_message() ] );
    }

    wp_send_json_success( [ 'booking_id' => $booking_id, 'message' => 'Reserva confirmada con éxito.' ] );
}
add_action( 'wp_ajax_bm_save_booking',        'bm_save_booking' );
add_action( 'wp_ajax_nopriv_bm_save_booking', 'bm_save_booking' );