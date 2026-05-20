<?php
/**
 * Página personalizada de recuperación de contraseña.
 * Soporta dos estados:
 *  1. Formulario de solicitud (step = 'request'): el usuario introduce su email.
 *  2. Formulario de nueva contraseña (step = 'reset'): viene de un enlace de email con key+login.
 */

$error   = '';
$success = '';
$step    = 'request'; // 'request' | 'reset'

// Determinar si estamos en paso de reset (link del email)
// wp-login.php?action=rp&key=...&login=... → redirigido a /lost-password?key=...&login=...
$reset_key   = isset( $_GET['key'] )   ? sanitize_text_field( wp_unslash( $_GET['key'] ) )   : '';
$reset_login = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';

// Si ya está logueado y NO viene con un token de reseteo, lo redirigimos al inicio
if ( is_user_logged_in() && empty( $reset_key ) ) {
    wp_redirect( home_url( '/' ) );
    exit;
}

if ( $reset_key && $reset_login ) {
    $step = 'reset';
    $user = check_password_reset_key( $reset_key, $reset_login );
    if ( is_wp_error( $user ) ) {
        $error = 'This password reset link has expired or is invalid. Please request a new one.';
        $step  = 'request';
        $reset_key   = '';
        $reset_login = '';
    }
}

// Procesar formulario de SOLICITUD
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['lp_request_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['lp_request_nonce'], 'neuvo_lost_password_request' ) ) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $user_email = sanitize_email( $_POST['user_email'] ?? '' );
        if ( empty( $user_email ) ) {
            $error = 'Please enter your email address.';
        } else {
            // Usamos la API nativa de WordPress para enviar el correo de reset
            $result = retrieve_password( $user_email );
            if ( is_wp_error( $result ) ) {
                // No revelamos si el email existe o no (seguridad)
                $success = 'If an account exists for that email, a reset link has been sent. Please check your inbox.';
            } else {
                $success = 'If an account exists for that email, a reset link has been sent. Please check your inbox.';
            }
        }
    }
}

// Procesar formulario de NUEVA CONTRASEÑA (paso 2)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['lp_reset_nonce'] ) && $step === 'reset' ) {
    if ( ! wp_verify_nonce( $_POST['lp_reset_nonce'], 'neuvo_lost_password_reset' ) ) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $new_pass  = $_POST['new_password']     ?? '';
        $new_pass2 = $_POST['new_password2']    ?? '';
        $r_key     = sanitize_text_field( $_POST['reset_key']   ?? '' );
        $r_login   = sanitize_text_field( $_POST['reset_login'] ?? '' );

        if ( empty( $new_pass ) || empty( $new_pass2 ) ) {
            $error = 'Please fill in both password fields.';
        } elseif ( strlen( $new_pass ) < 8 ) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ( $new_pass !== $new_pass2 ) {
            $error = 'Passwords do not match. Please try again.';
        } else {
            $user = check_password_reset_key( $r_key, $r_login );
            if ( is_wp_error( $user ) ) {
                $error = 'This reset link has expired. Please request a new one.';
                $step  = 'request';
            } else {
                reset_password( $user, $new_pass );
                $success = 'Your password has been updated successfully. You can now sign in.';
                $step    = 'request'; // volver al paso 1 con mensaje de éxito
                $reset_key   = '';
                $reset_login = '';
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEUVO – <?php echo $step === 'reset' ? 'Set New Password' : 'Forgot Password'; ?></title>
    <meta name="description" content="Reset your NEUVO account password securely.">
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/style.css' ); ?>">

    <style>
        /* ── Placeholder fix ── */
        input::placeholder                      { color: #999999 !important; opacity: 1 !important; }
        input::-webkit-input-placeholder        { color: #999999 !important; opacity: 1 !important; }
        input:-moz-placeholder                  { color: #999999 !important; opacity: 1 !important; }
        input::-moz-placeholder                 { color: #999999 !important; opacity: 1 !important; }
        input:-ms-input-placeholder             { color: #999999 !important; opacity: 1 !important; }

        /* ── Strength bar ── */
        .lp-strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #262626;
            margin-top: 8px;
            overflow: hidden;
        }
        .lp-strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.3s ease, background 0.3s ease;
        }
        .lp-strength-label {
            font-size: 0.7rem;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #737373;
            margin-top: 5px;
        }

        /* Password toggle */
        .lp-pw-wrapper {
            position: relative;
        }
        .lp-pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #737373;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            font-size: 1rem;
            transition: color 0.2s;
            z-index: 5;
        }
        .lp-pw-toggle:hover { color: #ffffff; }

        /* Info box */
        .lp-hint-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            margin-bottom: 28px;
            font-size: 0.82rem;
            color: #a3a3a3;
            line-height: 1.55;
            font-family: 'Montserrat', sans-serif;
        }
        .lp-hint-box i { font-size: 1rem; color: #737373; margin-top: 2px; flex-shrink: 0; }

        /*  Success message override */
        .alert-success-dark {
            background: rgba(3, 103, 30, 0.15);
            border: 1px solid rgba(255,255,255,0.12);
            color: #86efac;
            border-radius: 8px;
        }

        /* Reset image (same as register) */
        .reset-page .login-image {
            background-image: url("<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/login-car.jpg' ); ?>");
            min-height: 480px;
        }

        /* Login subtitle spacing */
        .login-subtitle { margin-bottom: 28px; }
    </style>

    <?php wp_head(); ?>
</head>

<body class="login-page reset-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="login-container">
                    <div class="row g-0">

                        <!-- Left image panel -->
                        <div class="col-md-6 d-none d-md-block">
                            <div class="login-image"></div>
                        </div>

                        <!-- Right form panel -->
                        <div class="col-md-6">
                            <div class="login-form-container">

                                <!-- Logo -->
                                <div class="text-center">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="login-logo brand-font">
                                        <?php bloginfo( 'name' ); ?>
                                    </a>
                                </div>

                                <?php if ( $step === 'request' ) : ?>
                                    <!--  STEP 1: Request reset -->
                                    <h1 class="login-title">Forgot Password</h1>
                                    <p class="login-subtitle">Enter your account email and we'll send you a link to reset your password.</p>

                                    <?php if ( $error ) : ?>
                                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span><?php echo esc_html( $error ); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $success ) : ?>
                                        <div class="alert alert-success-dark d-flex align-items-center gap-2 mb-4" role="alert">
                                            <i class="bi bi-check-circle"></i>
                                            <span><?php echo esc_html( $success ); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! $success ) : ?>
                                        <div class="lp-hint-box">
                                            <i class="bi bi-shield-lock"></i>
                                            <span>We'll send a secure link to your email. The link expires in <strong style="color:#d4d4d4;">60 minutes</strong>.</span>
                                        </div>

                                        <form method="POST" action="" id="lp-request-form">
                                            <?php wp_nonce_field( 'neuvo_lost_password_request', 'lp_request_nonce' ); ?>

                                            <div class="mb-4">
                                                <label for="lp-email" class="form-label-dark">Email Address</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark border-secondary text-secondary">
                                                        <i class="bi bi-envelope"></i>
                                                    </span>
                                                    <input
                                                        type="email"
                                                        name="user_email"
                                                        id="lp-email"
                                                        autocomplete="email"
                                                        style="color:#ffffff !important; background-color:rgba(0,0,0,0.55) !important;"
                                                        class="form-control form-control-dark border-start-0 ps-2"
                                                        placeholder="your@email.com"
                                                        required
                                                        value="<?php echo isset( $_POST['user_email'] ) ? esc_attr( $_POST['user_email'] ) : ''; ?>"
                                                    >
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-cta w-100 mt-1 d-flex justify-content-center align-items-center gap-2">
                                                <i class="bi bi-send"></i>
                                                Send Reset Link
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                <?php else : ?>
                                    <!-- STEP 2: Set new password -->
                                    <h1 class="login-title">Set New Password</h1>
                                    <p class="login-subtitle">Choose a strong password for your account.</p>

                                    <?php if ( $error ) : ?>
                                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span><?php echo esc_html( $error ); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="" id="lp-reset-form">
                                        <?php wp_nonce_field( 'neuvo_lost_password_reset', 'lp_reset_nonce' ); ?>
                                        <input type="hidden" name="reset_key"   value="<?php echo esc_attr( $reset_key ); ?>">
                                        <input type="hidden" name="reset_login" value="<?php echo esc_attr( $reset_login ); ?>">

                                        <div class="mb-3">
                                            <label for="lp-new-password" class="form-label-dark">New Password</label>
                                            <div class="input-group lp-pw-wrapper">
                                                <span class="input-group-text bg-dark border-secondary text-secondary">
                                                    <i class="bi bi-lock"></i>
                                                </span>
                                                <input
                                                    type="password"
                                                    name="new_password"
                                                    id="lp-new-password"
                                                    autocomplete="new-password"
                                                    style="color:#ffffff !important; background-color:rgba(0,0,0,0.55) !important;"
                                                    class="form-control form-control-dark border-start-0 ps-2"
                                                    placeholder="At least 8 characters"
                                                    required
                                                >
                                                <button type="button" class="lp-pw-toggle" id="lp-toggle-new" aria-label="Show password" title="Show/Hide">
                                                    <i class="bi bi-eye" id="lp-toggle-new-icon"></i>
                                                </button>
                                            </div>
                                            <!-- Strength indicator -->
                                            <div class="lp-strength-bar">
                                                <div class="lp-strength-fill" id="lp-strength-fill"></div>
                                            </div>
                                            <div class="lp-strength-label" id="lp-strength-label"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="lp-new-password2" class="form-label-dark">Confirm Password</label>
                                            <div class="input-group lp-pw-wrapper">
                                                <span class="input-group-text bg-dark border-secondary text-secondary">
                                                    <i class="bi bi-lock-fill"></i>
                                                </span>
                                                <input
                                                    type="password"
                                                    name="new_password2"
                                                    id="lp-new-password2"
                                                    autocomplete="new-password"
                                                    style="color:#ffffff !important; background-color:rgba(0,0,0,0.55) !important;"
                                                    class="form-control form-control-dark border-start-0 ps-2"
                                                    placeholder="Repeat your password"
                                                    required
                                                >
                                                <button type="button" class="lp-pw-toggle" id="lp-toggle-new2" aria-label="Show confirm password" title="Show/Hide">
                                                    <i class="bi bi-eye" id="lp-toggle-new2-icon"></i>
                                                </button>
                                            </div>
                                            <div class="lp-strength-label" id="lp-match-label" style="margin-top:5px;"></div>
                                        </div>

                                        <button type="submit" class="btn btn-cta w-100 mt-1 d-flex justify-content-center align-items-center gap-2">
                                            <i class="bi bi-check-circle"></i>
                                            Update Password
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Bottom links -->
                                <div class="text-center mt-4">
                                    <span class="text-secondary login-toggle-text">Remember your password? </span>
                                    <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="text-white text-decoration-none login-toggle-link">Sign In</a>
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

    <script>
    (function () {
        // ── Password visibility toggles ──
        function initToggle(btnId, inputId, iconId) {
            var btn   = document.getElementById(btnId);
            var input = document.getElementById(inputId);
            var icon  = document.getElementById(iconId);
            if (!btn || !input || !icon) return;
            btn.addEventListener('click', function () {
                var isHidden = input.type === 'password';
                input.type   = isHidden ? 'text' : 'password';
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
        initToggle('lp-toggle-new',  'lp-new-password',  'lp-toggle-new-icon');
        initToggle('lp-toggle-new2', 'lp-new-password2', 'lp-toggle-new2-icon');

        // ── Strength indicator ──
        var pwInput = document.getElementById('lp-new-password');
        var fill    = document.getElementById('lp-strength-fill');
        var label   = document.getElementById('lp-strength-label');

        function getStrength(pw) {
            var score = 0;
            if (pw.length >= 8)  score++;
            if (pw.length >= 12) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;
            return score;
        }

        var levels = [
            { pct: 0,   color: '#262626', text: '' },
            { pct: 20,  color: '#e53e3e', text: 'Very Weak' },
            { pct: 40,  color: '#f97316', text: 'Weak' },
            { pct: 60,  color: '#eab308', text: 'Fair' },
            { pct: 80,  color: '#22c55e', text: 'Strong' },
            { pct: 100, color: '#16a34a', text: 'Very Strong' },
        ];

        if (pwInput && fill && label) {
            pwInput.addEventListener('input', function () {
                var s = getStrength(this.value);
                var lvl = levels[s] || levels[0];
                fill.style.width    = lvl.pct + '%';
                fill.style.background = lvl.color;
                label.textContent   = this.value.length ? lvl.text : '';
                label.style.color   = lvl.color;
                checkMatch();
            });
        }

        // ── Match indicator ──
        var pw2Input   = document.getElementById('lp-new-password2');
        var matchLabel = document.getElementById('lp-match-label');

        function checkMatch() {
            if (!pw2Input || !matchLabel || !pwInput) return;
            if (!pw2Input.value) { matchLabel.textContent = ''; return; }
            if (pw2Input.value === pwInput.value) {
                matchLabel.textContent = '✓ Passwords match';
                matchLabel.style.color = '#22c55e';
            } else {
                matchLabel.textContent = '✗ Passwords do not match';
                matchLabel.style.color = '#e53e3e';
            }
        }
        if (pw2Input) pw2Input.addEventListener('input', checkMatch);

        // ── Client-side form validation (reset step) ──
        var resetForm = document.getElementById('lp-reset-form');
        if (resetForm) {
            resetForm.addEventListener('submit', function (e) {
                var p1 = document.getElementById('lp-new-password');
                var p2 = document.getElementById('lp-new-password2');
                if (!p1 || !p2) return;
                if (p1.value.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters.');
                    p1.focus();
                    return;
                }
                if (p1.value !== p2.value) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    p2.focus();
                }
            });
        }
    })();
    </script>
</body>
</html>
