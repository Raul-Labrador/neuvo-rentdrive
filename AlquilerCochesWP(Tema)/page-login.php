<?php

// Si ya está logueado, redirigir
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/' ) );
    exit;
}

// Procesar el formulario
$error   = '';
$success = '';

// Usamos un redirect_to opcional para volver a la página original después del login.
$redirect_to = function_exists( 'alquilercocheswp_get_login_redirect' ) ? alquilercocheswp_get_login_redirect() : home_url( '/' );

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['login_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['login_nonce'], 'neuvo_custom_login' ) ) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $credentials = array(
            'user_login'    => sanitize_text_field( $_POST['user_email'] ),
            'user_password' => $_POST['user_password'],
            'remember'      => isset( $_POST['remember'] ),
        );

        $user = wp_signon( $credentials, false );

        if ( is_wp_error( $user ) ) {
            $error = 'Incorrect email or password. Please try again.';
        } else {
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            // Si el usuario es administrador, redirigir al Dashboard de Laravel con SSO
            if ( in_array( 'administrator', (array) $user->roles, true ) ) {
                if ( function_exists( 'alquilercocheswp_get_laravel_dashboard_url' ) ) {
                    $laravel_url = alquilercocheswp_get_laravel_dashboard_url( $user );
                } else {
                    $laravel_url = 'https://neuvo-app.com/';
                }
                error_log( 'Admin login — redirecting to Laravel: ' . $laravel_url );
                wp_redirect( $laravel_url );
                exit;
            }

            $redirect_to = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), home_url( '/' ) ) : $redirect_to;
            if ( ! $redirect_to ) {
                $redirect_to = home_url( '/' );
            }
            error_log( 'Login redirect to: ' . $redirect_to );
            wp_safe_redirect( $redirect_to );
            exit;
        }
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEUVO - Login</title>

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

<body class="login-page">
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

                                <h1 class="login-title">Sign In</h1>
                                <p class="login-subtitle">Sign in to your account to manage your premium reservations.</p>

                                <?php if ( $error ) : ?>
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span><?php echo esc_html( $error ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <?php wp_nonce_field( 'neuvo_custom_login', 'login_nonce' ); ?>
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">

                                    <div class="mb-3">
                                        <label for="email" class="form-label-dark">Email or Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope"></i></span>
                                            <input type="text" name="user_email" autocomplete="username" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="email" placeholder="Type your email or username here" required value="<?php echo isset( $_POST['user_email'] ) ? esc_attr( $_POST['user_email'] ) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label-dark">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock"></i></span>
                                            <input type="password" name="user_password" autocomplete="current-password" style="color: #ffffff !important; background-color: rgba(0,0,0,0.55) !important;" class="form-control form-control-dark border-start-0 ps-2" id="password" placeholder="Type your password here" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-cta w-100 mt-3 d-flex justify-content-center align-items-center text-center">Log In</button>
                                </form>

                                <div class="text-center mt-4">
                                    <span class="text-secondary login-toggle-text">Don't have an account? </span>
                                    <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="text-white text-decoration-none login-toggle-link">Sign Up</a>
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

