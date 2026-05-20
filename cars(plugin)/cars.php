<?php
/**
 * Plugin Name: CPT-Cars
 * Plugin URI:  https://tusitio.com/plugins/cars/
 * Description: Plugin para el registro de coches con sus características mediante CPT
 * Version:     0.1.0
 * Author:      Grupo HMJR
 * Author URI:  http://grupohmjr.example.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cars-cpt
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

// Via muerta para dar seguridad
defined('ABSPATH') or die('You cannot access this file...sorry not sorry');

if (!defined('RENTWAY_LARAVEL_API_BASE')) {
    define('RENTWAY_LARAVEL_API_BASE', 'https://admin.neuvo-app.com/api');
}

class Cars
{
    function __construct()
    {
        // Añadimos shortcodes para mostrar los campos personalizados en la interfaz
        add_shortcode('rlp_show_main_fields', array($this, 'rlp_show_main_fields'));
        add_shortcode('rlp_show_all_fields', array($this, 'rlp_show_all_fields'));
    }

    // Funcionalidad del plugin
    function execute_actions()
    {
        // Registramos el CPT del coche
        add_action('init', array($this, 'rlp_cars_register'));

        // Registramos la taxonomía (Facilitará el uso de la API de laravel)
        add_action('init', array($this, 'rlp_taxonomy_register'));

        // Crea un meta_box para mostrar los campos personalizados
        add_action('add_meta_boxes', array($this, 'rlp_add_meta_box'));

        // Guardar estos campos en al base de datos
        add_action('save_post', array($this, 'rlp_save_custom_fields'));

        // Guardar los datos en un json
        add_action('rest_api_init', array($this, 'rlp_register_rest_fields'));

        // Añadir JS y CSS para el admin-area
        add_action('admin_enqueue_scripts', array($this, 'rlp_attach_script'));

        // Añadir JS y CSS para la interfaz
        add_action('wp_enqueue_scripts', array($this, 'rlp_attach_script_front'));

        //Carga los archivos de traducción (.mo y .po) para que el plugin sea internacional (i18n)
        add_action('plugins_loaded', array($this, 'rlp_load_textdomain'));

        // Crea el acceso y la página de configuración dentro del panel de administración de WordPress
        add_action('admin_menu', array($this, 'rlp_add_admin_menu'));

        // Registra los campos y secciones de configuración en la base de datos (Settings API) para permitir la persistencia de datos
        add_action('admin_init', array($this, 'rlp_register_settings'));
    }

    function rlp_cars_register()
    {
        // Activamos los paneles correspondientes al CPT 
        $supports = array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'author',
            'comments'
        );

        // Hay que especificar las etiquetas correspondiente al CPT del admin area
        $labels = array(
            'name' => _x('Cars', 'plural'),
            'singular_name' => _x('Car', 'singular'),
            'menu_name' => _x('Cars', 'admin menu'),
            'menu_admin_bar' => _x('Cars', 'admin bar'),
            'add_new' => _x('Add New Car', 'add_new'),
            'all_items' => __('All Cars'),
            'add_new_item' => __('Add new Car'),
            'view_item' => __('View Car'),
            'search' => __('Search Cars'),
            'not_found' => __('No cars found...'),
        );

        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'wp_query' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'cars'),
            'show_in_rest' => true,
            'has_archive' => false,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-car',
        );

        // El nombre de un CTA tiene que observar: 1º No puede tener más de 12 caracteres.
        // 2º Tiene que tener el formato de SLUG.
        // 3º Tiene que ser un nombre identificativo

        // Registramos el CPT
        register_post_type('cars', $args);
    }

    function rlp_taxonomy_register()
    {
        $labels = array(
            'name' => _x('Car Types', 'taxonomy general name'),
            'singular_name' => _x('Car Type', 'taxonomy singular name'),
            'search_items' => __('Search Car Types'),
            'all_items' => __('All Car Types'),
            'parent_item' => __('Parent Car Type'),
            'parent_item_colon' => __('Parent Car Type:'),
            'edit_item' => __('Edit Car Type'),
            'update_item' => __('Update Car Type'),
            'add_new_item' => __('Add New Car Type'),
            'new_item_name' => __('New Car Type Name'),
            'menu_name' => __('Car Type'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true, // VITAL para que Laravel la vea por API
            'rewrite' => array('slug' => 'car-type'),
        );

        register_taxonomy('car_type', array('cars'), $args);
    }

    function rlp_add_meta_box($screens)
    {
        $screens = array('cars');

        foreach ($screens as $screen) {
            add_meta_box('project-metabox', 'NEUVO·Cars', array($this, 'rlp_metabox_callback'), $screen, 'advanced');
        }
    }

    function rlp_metabox_callback($post)
    {
        // Creamos un mecanismo de validación de datos para ejecuciones procedentes de fuera de mi sitio web
        wp_nonce_field(basename(__FILE__), 'cars-nonce');

        // Data Harvesting
        $brand = get_post_meta($post->ID, 'rlp_car_plate', true);
        $brand = get_post_meta($post->ID, 'rlp_car_brand', true);
        $model = get_post_meta($post->ID, 'rlp_car_model', true);
        $year = get_post_meta($post->ID, 'rlp_car_year', true);
        $price = get_post_meta($post->ID, 'rlp_car_price', true);
        $fuel = get_post_meta($post->ID, 'rlp_car_fuel', true);
        $km = get_post_meta($post->ID, 'rlp_car_km', true);
        $transmission = get_post_meta($post->ID, 'rlp_car_transmission', true); //Automatico o manual
        $engine_displacement = get_post_meta($post->ID, 'rlp_car_ed', true); //Cilindrada
        $horsepower = get_post_meta($post->ID, 'rlp_car_horsepower', true);
        $emissions = get_post_meta($post->ID, 'rlp_car_emissions', true); //Etiquetas
        $doors = get_post_meta($post->ID, 'rlp_car_doors', true);
        $seats = get_post_meta($post->ID, 'rlp_car_seats', true);
        $body = get_post_meta($post->ID, 'rlp_car_body', true); // Sedan, Hatchback, SUV, Coupe, Convertible, Wagon.
        $trunk = get_post_meta($post->ID, 'rlp_car_trunk', true); //Capacidad de maletero
        $color = get_post_meta($post->ID, 'rlp_car_color', true);


        // SERVICES
        $services = get_post_meta($post->ID, 'rlp_services', true);


        ?>
        <!-- Dibujamos los campos con etiquetas HTML de formulario para facilitar su introducción y guardado -->
        <div class="flex-metabox">
            <div class="details">
                <h2>Car Details</h2>
                <div class="rlp-car-plate">
                    <label for="rlp_car_plate">Plate</label>
                    <input type="text" name="rlp_car_plate" id="rlp_car_plate" value="<?php echo $brand; ?>">
                </div>
                <div class="rlp-car-brand">
                    <label for="rlp_car_brand">Brand</label>
                    <input type="text" name="rlp_car_brand" id="rlp_car_brand" value="<?php echo $brand; ?>">
                </div>
                <div class="rlp-car-model">
                    <label for="rlp_car_model">Model</label>
                    <input type="text" name="rlp_car_model" id="rlp_car_model" value="<?php echo $model; ?>">
                </div>
                <div class="rlp-car-year">
                    <label for="rlp_car_year">Year</label>
                    <input type="text" name="rlp_car_year" id="rlp_car_year" value="<?php echo $year; ?>">
                </div>
                <div class="rlp-car-price">
                    <label for="rlp_car_price">Price</label>
                    <input type="text" name="rlp_car_price" id="rlp_car_price" value="<?php echo $price; ?>">
                </div>
                <div class="rlp-car-fuel">
                    <label for="rlp_car_fuel">Fuel</label>
                    <select name="rlp_car_fuel" id="rlp_car_fuel">
                        <option value="Choose a type of fuel" <?php if ($fuel == "Choose a type of fuel")
                            echo "selected"; ?>>
                            Choose a type of fuel</option>
                        <option value="Gasoline" <?php if ($fuel == "Gasoline")
                            echo "selected"; ?>>Gasoline</option>
                        <option value="Diesel" <?php if ($fuel == "Diesel")
                            echo "selected"; ?>>Diesel</option>
                        <option value="Electric" <?php if ($fuel == "Electric")
                            echo "selected"; ?>>Electric</option>
                        <option value="Hybrid" <?php if ($fuel == "Hybrid")
                            echo "selected"; ?>>Hybrid</option>
                        <option value="PHEV" <?php if ($fuel == "PHEV")
                            echo "selected"; ?>>Plug-in Hybrid</option>
                        <option value="MHEV" <?php if ($fuel == "MHEV")
                            echo "selected"; ?>>Mild Hybrid</option>
                        <option value="LPG" <?php if ($fuel == "LPG")
                            echo "selected"; ?>>Liquefied Petroleum Gas</option>
                        <option value="CNG" <?php if ($fuel == "CNG")
                            echo "selected"; ?>>Compressed Natural Gas</option>
                        <option value="Hydrogen" <?php if ($fuel == "Hydrogen")
                            echo "selected"; ?>>Hydrogen</option>
                    </select>
                </div>
                <div class="rlp-car-km">
                    <label for="rlp_car_km">Kilometers</label>
                    <input type="text" name="rlp_car_km" id="rlp_car_km" value="<?php echo $km; ?>">
                </div>
                <div class="rlp-car-transmission">
                    <label for="rlp_car_transmission">Transmission</label>
                    <select name="rlp_car_transmission" id="rlp_car_transmission">
                        <option value="Choose a type of transmission" <?php if ($transmission == "Choose a type of transmission")
                            echo "selected"; ?>>Choose a type of transmission</option>
                        <option value="Automatic" <?php if ($transmission == "Automatic")
                            echo "selected"; ?>>Automatic</option>
                        <option value="Manual" <?php if ($transmission == "Manual")
                            echo "selected"; ?>>Manual</option>
                    </select>
                </div>
                <div class="rlp-car-ed">
                    <label for="rlp_car_ed">Engine Displacement</label>
                    <input type="text" name="rlp_car_ed" id="rlp_car_ed" value="<?php echo $engine_displacement; ?>">
                </div>
                <div class="rlp-car-horsepower">
                    <label for="rlp_car_horsepower">Horsepower</label>
                    <input type="text" name="rlp_car_horsepower" id="rlp_car_horsepower" value="<?php echo $horsepower; ?>">
                </div>
                <div class="rlp-car-emissions">
                    <label for="rlp_car_emissions">Emissions</label>
                    <select name="rlp_car_emissions" id="rlp_car_emissions">
                        <option value="Choose a sticker" <?php if ($emissions == "Choose a sticker")
                            echo "selected"; ?>>Choose
                            a sticker</option>
                        <option value="CERO" <?php if ($emissions == "CERO")
                            echo "selected"; ?>>CERO</option>
                        <option value="ECO" <?php if ($emissions == "ECO")
                            echo "selected"; ?>>ECO</option>
                        <option value="C" <?php if ($emissions == "C")
                            echo "selected"; ?>>C</option>
                        <option value="B" <?php if ($emissions == "B")
                            echo "selected"; ?>>B</option>
                    </select>
                </div>
                <div class="rlp-car-doors">
                    <label for="rlp_car_doors">Doors</label>
                    <input type="text" name="rlp_car_doors" id="rlp_car_doors" value="<?php echo $doors; ?>">
                </div>
                <div class="rlp-car-seats">
                    <label for="rlp_car_seats">Seats</label>
                    <input type="text" name="rlp_car_seats" id="rlp_car_seats" value="<?php echo $seats; ?>">
                </div>
                <div class="rlp-car-body">
                    <label for="rlp_car_body">Body</label>
                    <select name="rlp_car_body" id="rlp_car_body">
                        <option value="Choose a body type" <?php if ($body == "Choose a body type")
                            echo "selected"; ?>>Choose a
                            body type</option>
                        <option value="berlina" <?php if ($body == "berlina")
                            echo "selected"; ?>>Berlina</option>
                        <option value="familiar" <?php if ($body == "familiar")
                            echo "selected"; ?>>Familiar</option>
                        <option value="coupe" <?php if ($body == "coupe")
                            echo "selected"; ?>>Coupe</option>
                        <option value="suv" <?php if ($body == "suv")
                            echo "selected"; ?>>SUV</option>
                        <option value="minivan" <?php if ($body == "minivan")
                            echo "selected"; ?>>Minivan</option>
                        <option value="cabrio" <?php if ($body == "cabrio")
                            echo "selected"; ?>>Cabrio</option>
                        <option value="pick-up" <?php if ($body == "pick-up")
                            echo "selected"; ?>>Pick Up</option>
                    </select>
                </div>
                <div class="rlp-car-trunk">
                    <label for="rlp_car_trunk">Trunk</label>
                    <select name="rlp_car_trunk" id="rlp_car_trunk">
                        <option value="Choose trunk size" <?php if ($trunk == "Choose trunk size")
                            echo "selected"; ?>>Choose
                            trunk size</option>
                        <option value="big" <?php if ($trunk == "big")
                            echo "selected"; ?>>Big > 400L</option>
                        <option value="medium" <?php if ($trunk == "medium")
                            echo "selected"; ?>>Medium 200L - 400L</option>
                        <option value="small" <?php if ($trunk == "small")
                            echo "selected"; ?>>Small < 200L</option>
                    </select>
                </div>
                <div class="rlp-car-color">
                    <label for="rlp_car_color">Color</label>
                    <input type="text" name="rlp_car_color" id="rlp_car_color" value="<?php echo $color; ?>">
                </div>
            </div>
            <div class="services">
                <h2>Included Services</h2>
                <?php
                require_once(plugin_dir_path(__FILE__) . 'inc/services.php');
                ?>
            </div>
        </div>

        <?php
    }

    function rlp_save_custom_fields($post_id)
    {
        //Tenemos que seguir los tres pasos claves.
        // Solo puedo grabar el post si no estoy en revisión, no estoy en autosave y es válido el campo nonce

        // Primer paso, tenemos que ver si estamos en revisión
        $is_revision = wp_is_post_revision($post_id);

        // Segundo paso, comrpobamos si estamos en autosave
        $is_autosave = wp_is_post_autosave($post_id);

        // Tercer paso, comprobamos si es válido el campo nonce
        if (isset($_POST['cars-nonce'])) {
            $is_valid_nonce = wp_verify_nonce($_POST['cars-nonce'], basename(__FILE__));

            if ($is_revision || $is_autosave || !$is_valid_nonce) {
                return;
            }

            // Además, tenemos que comprobar si el usuario tiene capacidades para editar el post
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            // Saneamos los datos
            $plate = sanitize_text_field($_POST['rlp_car_plate']);
            $brand = sanitize_text_field($_POST['rlp_car_brand']);
            $model = sanitize_text_field($_POST['rlp_car_model']);
            $year = sanitize_text_field($_POST['rlp_car_year']);
            $price = sanitize_text_field($_POST['rlp_car_price']);
            $fuel = sanitize_text_field($_POST['rlp_car_fuel']);
            $km = sanitize_text_field($_POST['rlp_car_km']);
            $transmission = sanitize_text_field($_POST['rlp_car_transmission']);
            $engine_displacement = sanitize_text_field($_POST['rlp_car_ed']);
            $horsepower = sanitize_text_field($_POST['rlp_car_horsepower']);
            $emissions = sanitize_text_field($_POST['rlp_car_emissions']);
            $doors = sanitize_text_field($_POST['rlp_car_doors']);
            $seats = sanitize_text_field($_POST['rlp_car_seats']);
            $body = sanitize_text_field($_POST['rlp_car_body']);
            $trunk = sanitize_text_field($_POST['rlp_car_trunk']);
            $color = sanitize_text_field($_POST['rlp_car_color']);

            //Los guardamos en la base de datos
            update_post_meta($post_id, 'rlp_car_brand', $plate);
            update_post_meta($post_id, 'rlp_car_model', $model);
            update_post_meta($post_id, 'rlp_car_year', $year);
            update_post_meta($post_id, 'rlp_car_price', $price);
            update_post_meta($post_id, 'rlp_car_fuel', $fuel);
            update_post_meta($post_id, 'rlp_car_km', $km);
            update_post_meta($post_id, 'rlp_car_transmission', $transmission);
            update_post_meta($post_id, 'rlp_car_ed', $engine_displacement);
            update_post_meta($post_id, 'rlp_car_horsepower', $horsepower);
            update_post_meta($post_id, 'rlp_car_emissions', $emissions);
            update_post_meta($post_id, 'rlp_car_doors', $doors);
            update_post_meta($post_id, 'rlp_car_seats', $seats);
            update_post_meta($post_id, 'rlp_car_body', $body);
            update_post_meta($post_id, 'rlp_car_trunk', $trunk);
            update_post_meta($post_id, 'rlp_car_color', $color);

            // Guardamos los SERVICES en la base de datos
            if (isset($_POST['rlp_services'])) {
                $aux_services = array();
                foreach ($_POST['rlp_services'] as $row) {
                    if (!empty($row['service']) || !empty($row['description'])) {
                        $aux_services[] = array(
                            'service' => sanitize_text_field($row['service']),
                            'description' => sanitize_text_field($row['description'])
                        );
                    }
                }

                update_post_meta($post_id, 'rlp_services', $aux_services);
            }
        }
    }

    function rlp_register_rest_fields()
    {
        // Definimos todos los campos en un array
        $fields = array(
            'rlp_car_plate',
            'rlp_car_brand',
            'rlp_car_model',
            'rlp_car_year',
            'rlp_car_price',
            'rlp_car_fuel',
            'rlp_car_km',
            'rlp_car_transmission',
            'rlp_car_ed',
            'rlp_car_horsepower',
            'rlp_car_emissions',
            'rlp_car_doors',
            'rlp_car_seats',
            'rlp_car_body',
            'rlp_car_trunk',
            'rlp_car_color'
        );

        // Registramos cada campo del array (lectura + escritura)
        foreach ($fields as $field) {
            register_rest_field('cars', $field, array(
                'get_callback' => function ($post_array) use ($field) {
                    return get_post_meta($post_array['id'], $field, true);
                },
                'update_callback' => function ($value, $post, $field_name) {
                    update_post_meta($post->ID, $field_name, sanitize_text_field($value));
                },
                'schema' => null,
            ));
        }

        // Registramos el campo de servicios por separado (lectura + escritura)
        register_rest_field('cars', 'rlp_services', array(
            'get_callback' => function ($post_array) {
                return get_post_meta($post_array['id'], 'rlp_services', true);
            },
            'update_callback' => function ($value, $post) {
                if (is_array($value)) {
                    $sanitized = array();
                    foreach ($value as $row) {
                        if (!empty($row['service']) || !empty($row['description'])) {
                            $sanitized[] = array(
                                'service' => sanitize_text_field($row['service']),
                                'description' => sanitize_text_field($row['description']),
                            );
                        }
                    }
                    update_post_meta($post->ID, 'rlp_services', $sanitized);
                }
            },
            'schema' => null,
        ));
    }

    function rlp_attach_script()
    {
        // Cargar CSS
        wp_register_style('admin-css', plugins_url('/css/admin.css', __FILE__));
        wp_enqueue_style('admin-css');

        // Cargar JS 
        wp_register_script('admin-js', plugins_url('/js/admin.js', __FILE__));
        wp_enqueue_script('admin-js');
    }

    function rlp_attach_script_front()
    {
        wp_register_style('front-css', plugins_url('/css/front.css', __FILE__));
        wp_enqueue_style('front-css');

        if (is_singular('cars')) {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, false);
            wp_enqueue_style('booking-css', plugins_url('/css/booking.css', __FILE__), array(), '2.0.0');

            wp_enqueue_script('booking-js', plugins_url('/js/booking.js', __FILE__), array(), '2.0.2', true);

            $car_price = get_post_meta(get_the_ID(), 'rlp_car_price', true);
            $car_title = get_the_title();

            $current_user = wp_get_current_user();
            wp_localize_script('booking-js', 'bookingData', array(
                'restUrl' => esc_url_raw(rest_url('booking/v1/reserve')),
                'checkAvailabilityUrl' => esc_url_raw(rest_url('booking/v1/check-availability')),
                'nonce' => wp_create_nonce('wp_rest'),
                'pricePerDay' => floatval($car_price),
                'carTitle' => $car_title,
                'checkoutUrl' => home_url('/checkout'),
                'userName' => $current_user->display_name ?: ($current_user->user_login ?: ''),
                'userEmail' => $current_user->user_email ?: '',
                'stripePublicKey' => defined('STRIPE_PUBLIC_KEY') ? STRIPE_PUBLIC_KEY : '',
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'ajaxNonce' => wp_create_nonce('bm_pay_nonce'),
                'isLoggedIn' => is_user_logged_in(),
            ));
        }

        // Recursos de pago: solo en la página de pago
        if (is_page('checkout')) {
            wp_enqueue_style('checkout-css', plugins_url('/css/checkout.css', __FILE__), array(), '1.0.0');

            wp_enqueue_script('checkout-js', plugins_url('/js/checkout.js', __FILE__), array(), '1.0.0', true);
            wp_localize_script('checkout-js', 'checkoutData', array(
                'restUrl' => esc_url_raw(rest_url('booking/v1/reserve')),
                'nonce' => wp_create_nonce('wp_rest'),
                'carsUrl' => home_url('/cars'),
            ));
        }
    }

    function rlp_show_main_fields($atts)
    {
        // Esto es propio de los shortcodes, es la manera de sacar el id
        $args = shortcode_atts(
            array(
                'id' => 0,

            ),

            $atts,
        );

        $post_id = $args['id'];

        // Dibujamos algunos campos con etiquetas HTML
        ob_start();
        ?>
        <div class="custom-fields-container">
            <div class="cfield field-1"><span>Model:</span><span>
                    <?php echo get_post_meta($post_id, 'rlp_car_model', true); ?>
                </span></div>
            <div class="cfield field-2"><span>Price:</span><span>
                    <?php echo get_post_meta($post_id, 'rlp_car_price', true); ?>
                </span></div>
            <div class="cfield field-3"><span>Year:</span><span>
                    <?php echo get_post_meta($post_id, 'rlp_car_year', true); ?>
                </span></div>
            <div class="cfield field-4"><span>Kilometers:</span><span>
                    <?php echo get_post_meta($post_id, 'rlp_car_km', true); ?>
                </span></div>
        </div>
        <?php
        return ob_get_clean();
    }

    function rlp_show_all_fields($atts)
    {
        $args = shortcode_atts(array('id' => get_the_ID()), $atts);
        $post_id = $args['id'];

        $brand = get_post_meta($post_id, 'rlp_car_brand', true);
        $model = get_post_meta($post_id, 'rlp_car_model', true);
        $hp = get_post_meta($post_id, 'rlp_car_horsepower', true);
        $fuel = get_post_meta($post_id, 'rlp_car_fuel', true);

        ob_start();
        ?>
        <div class="all-fields-container">
            <h3>Technical Specifications</h3>
            <ul>
                <li><strong>Brand:</strong> <?php echo $brand; ?></li>
                <li><strong>Model:</strong> <?php echo $model; ?></li>
                <li><strong>HP:</strong> <?php echo $hp; ?></li>
                <li><strong>Fuel:</strong> <?php echo $fuel; ?></li>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    function rlp_load_textdomain()
    {
        load_plugin_textdomain('cars-cpt', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    function rlp_add_admin_menu()
    {
        add_options_page(
            'NEUVO Cars Settings',
            'NEUVO Cars',
            'manage_options',
            'neuvo-cars-settings',
            array($this, 'rlp_settings_page_html')
        );
    }

    function rlp_settings_page_html()
    {
        ?>
        <div class="wrap">
            <h1>NEUVO Cars Configuration</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('rlp_settings_group');
                do_settings_sections('neuvo-cars-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function rlp_register_settings()
    {
        // Registramos el ajuste "rlp_main_color" en el grupo "rlp_settings_group"
        register_setting('rlp_settings_group', 'rlp_main_color');

        // Creamos una sección dentro de la página de ajustes
        add_settings_section(
            'rlp_settings_main_section',
            __('Main Configuration', 'cars-cpt'),
            null,
            'neuvo-cars-settings'
        );

        // Añadimos el campo específico para elegir el color
        add_settings_field(
            'rlp_main_color',
            __('Featured Theme Color', 'cars-cpt'),
            array($this, 'rlp_color_field_render'),
            'neuvo-cars-settings',
            'rlp_settings_main_section'
        );
    }

    function rlp_color_field_render()
    {
        // Recuperamos el valor guardado en la base de datos o el color por defecto #0a0a0a
        $val = get_option('rlp_main_color', '#0a0a0a');

        // Dibujamos el input tipo color
        echo '<input type="color" name="rlp_main_color" value="' . esc_attr($val) . '">';
        echo '<p class="description">' . __('Choose the primary color for car cards and accents.', 'cars-cpt') . '</p>';
    }

} // Fin de la Clase Cars

// Ejecución del Plugin
if (class_exists('Cars')) {
    $project = new Cars();
    $project->execute_actions();
}

// Carga de módulos externos
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-availability.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-modal.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-booked-dates.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-user-reservations.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-cancel-reservation.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-return-details.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-report-incident.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-check-user.php';
require_once plugin_dir_path(__FILE__) . 'inc/booking-rest-auth-user.php';

register_activation_hook(__FILE__, 'rlp_flush_rewrite_rules');

function rlp_flush_rewrite_rules()
{
    $cars = new Cars();
    $cars->rlp_cars_register();

    global $wpdb;
    $table_name = $wpdb->prefix . 'neuvo_bookings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        car_id BIGINT(20) UNSIGNED NOT NULL,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    flush_rewrite_rules();
}