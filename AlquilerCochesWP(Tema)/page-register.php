<?php

// Si ya está logueado, redirigir
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/' ) );
    exit;
}

// Si el registro está desactivado, redirigir al login
if ( ! get_option( 'users_can_register' ) ) {
    wp_redirect( home_url( '/login' ) );
    exit;
}

$error   = '';
$success = '';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['register_nonce'] ) ) {
    error_log( 'Register: POST received, nonce field present' );
    
    if ( ! wp_verify_nonce( $_POST['register_nonce'], 'neuvo_custom_register' ) ) {
        $error = 'Security token invalid. Please try again.';
        error_log( 'Register: Nonce verification failed' );
    } else {
        error_log( 'Register: Nonce verified OK' );
        
        $fullname = sanitize_text_field( $_POST['fullname'] );
        $email    = sanitize_email( $_POST['user_email'] );
        $password = $_POST['user_password'];
        $repeat_password = $_POST['repeat_password'];

        error_log( 'Register: fullname=' . $fullname . ', email=' . $email . ', password_len=' . strlen( $password ) );

        // Validaciones básicas
        if ( empty( $fullname ) || empty( $email ) || empty( $password ) || empty( $repeat_password ) ) {
            $error = 'Please fill in all fields.';
            error_log( 'Register: Empty fields error' );
        } elseif ( $password !== $repeat_password ) {
            $error = 'Passwords do not match. Please try again.';
            error_log( 'Register: Passwords mismatch error' );
        } elseif ( ! is_email( $email ) ) {
            $error = 'Please enter a valid email address.';
            error_log( 'Register: Invalid email error' );
        } elseif ( strlen( $password ) < 6 ) {
            $error = 'Password must be at least 6 characters long.';
            error_log( 'Register: Short password error' );
        } else {
            // Generar username desde el email
            $username = sanitize_user( current( explode( '@', $email ) ) );

            // Si el username ya existe, añadir número aleatorio
            if ( username_exists( $username ) ) {
                $username = $username . rand( 100, 999 );
            }

            error_log( 'Register: Creating user with username=' . $username . ', email=' . $email );

            $user_id = wp_create_user( $username, $password, $email );

            error_log( 'Register: wp_create_user returned: ' . var_export( $user_id, true ) );

            if ( is_wp_error( $user_id ) ) {
                $error = $user_id->get_error_message();
                error_log( 'Register: User creation error - ' . $error );
            } else {
                error_log( 'Register: User created successfully with ID=' . $user_id );
                
                // Actualizar display name
                wp_update_user( array( 'ID' => $user_id, 'display_name' => $fullname ) );

                // Loguear al usuario automáticamente
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );

                error_log( 'Register: User logged in, redirecting to home' );

                wp_redirect( home_url( '/' ) );
                exit;
            }
        }
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEUVO - Sign Up</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/style.css' ); ?>">

    <style>
        input::placeholder {
            color: #999999 !important;
            opacity: 1 !important;
        }
        input::-webkit-input-placeholder {
            color: #999999 !important;
            opacity: 1 !important;
        }
        input:-moz-placeholder {
            color: #999999 !important;
            opacity: 1 !important;
        }
        input::-moz-placeholder {
            color: #999999 !important;
            opacity: 1 !important;
        }
        input:-ms-input-placeholder {
            color: #999999 !important;
            opacity: 1 !important;
        }
    </style>

    <?php wp_head(); ?>
</head>

<body class="login-page register-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="login-container">
                    <div class="row g-0">
                        <div class="col-md-6 d-none d-md-block">
                            <div class="login-image"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="login-form-container">
                                <div class="text-center">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="login-logo brand-font"><?php bloginfo( 'name' ); ?></a>
                                </div>

                                <h1 class="login-title">Sign Up</h1>
                                <p class="login-subtitle">Create an account to start your premium car rental experience.</p>

                                <!-- Mensajes de error -->
                                <?php if ( $error ) : ?>
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span><?php echo esc_html( $error ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Formulario de Registro -->
                                <form method="POST" action="">
                                    <?php wp_nonce_field( 'neuvo_custom_register', 'register_nonce' ); ?>

                                    <div class="mb-3">
                                        <label for="fullname" class="form-label-dark">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-person"></i></span>
                                            <input type="text" name="fullname" autocomplete="name" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="fullname" placeholder="Enter full name" required value="<?php echo isset( $_POST['fullname'] ) ? esc_attr( $_POST['fullname'] ) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label-dark">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="user_email" autocomplete="email" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="email" placeholder="Enter email address" required value="<?php echo isset( $_POST['user_email'] ) ? esc_attr( $_POST['user_email'] ) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label-dark">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock"></i></span>
                                            <input type="password" name="user_password" autocomplete="new-password" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="password" placeholder="Enter password" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="repeat_password" class="form-label-dark">Repeat Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" name="repeat_password" autocomplete="new-password" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="repeat_password" placeholder="Repeat your password" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-cta w-100 mt-3 d-flex justify-content-center align-items-center text-center">Create Account</button>
                                </form>

                                <div class="text-center mt-4">
                                    <span class="text-secondary login-toggle-text">Already have an account? </span>
                                    <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="text-white text-decoration-none login-toggle-link">Log In</a>
                                </div>

                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="back-to-home">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>

