<?php

// Redirigir a la página de inicio de sesión si no ha iniciado sesión.
if (!is_user_logged_in()) {
    wp_redirect(add_query_arg('redirect_to', rawurlencode(home_url('/my-reservations')), home_url('/login')));
    exit;
}

get_header();
$current_user = wp_get_current_user();
?>

<?php get_template_part('nav'); ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header-title">My Reservations</h1>
        <p class="page-header-breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a> / My Reservations
        </p>
    </div>
</section>

<section class="reservations-section">
    <div class="container">

        <div class="reservations-welcome-card">
            <div class="reservations-welcome-info">
                <div class="reservations-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h2 class="reservations-welcome-name">Welcome, <?php echo esc_html($current_user->display_name); ?>
                    </h2>
                    <p class="reservations-welcome-email"><i
                            class="bi bi-envelope me-2"></i><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>
            <div class="reservations-stats-row">
                <div class="reservations-stat-box">
                    <span class="reservations-stat-number" id="stat-total">—</span>
                    <span class="reservations-stat-label">Total</span>
                </div>
                <div class="reservations-stat-box">
                    <span class="reservations-stat-number" id="stat-confirmed">—</span>
                    <span class="reservations-stat-label">Confirmed</span>
                </div>
                <div class="reservations-stat-box">
                    <span class="reservations-stat-number" id="stat-completed">—</span>
                    <span class="reservations-stat-label">Completed</span>
                </div>
                <div class="reservations-stat-box">
                    <span class="reservations-stat-number" id="stat-cancelled">—</span>
                    <span class="reservations-stat-label">Cancelled</span>
                </div>
            </div>
        </div>

        <div class="reservations-filter-bar">
            <button class="reservations-filter-btn active" data-filter="all">
                <i class="bi bi-grid me-2"></i>All
            </button>
            <button class="reservations-filter-btn" data-filter="confirmed">
                <i class="bi bi-check-circle me-2"></i>Confirmed
            </button>
            <button class="reservations-filter-btn" data-filter="completed">
                <i class="bi bi-trophy me-2"></i>Completed
            </button>
            <button class="reservations-filter-btn" data-filter="cancelled">
                <i class="bi bi-x-circle me-2"></i>Cancelled
            </button>
        </div>

        <div id="reservations-loading" class="reservations-loading">
            <div class="reservations-spinner"></div>
            <p>Loading your reservations...</p>
        </div>

        <div id="reservations-empty" class="reservations-empty" style="display:none;">
            <i class="bi bi-calendar-x"></i>
            <h3>No Reservations Yet</h3>
            <p>You haven't made any reservations yet. Explore our fleet and book your dream car today!</p>
            <a href="<?php echo home_url('/cars'); ?>" class="btn-cta">Browse Cars</a>
        </div>

        <div id="reservations-grid" class="reservations-grid" style="display:none;">
            <!-- Las tarjetas se insertarán aquí mediante JS. -->
        </div>

        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content reservations-modal-content">
                    <div class="modal-header reservations-modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Cancel Reservation
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body reservations-modal-body">
                        <p>Are you sure you want to cancel this reservation? This action cannot be undone.</p>
                        <div class="reservations-modal-detail" id="cancel-detail">
                            <!-- LLenado dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer reservations-modal-footer">
                        <button type="button" class="btn reservations-btn-secondary" data-bs-dismiss="modal">Keep
                            Reservation</button>
                        <button type="button" class="btn reservations-btn-danger" id="confirm-cancel-btn">
                            <i class="bi bi-x-circle me-1"></i>Yes, Cancel It
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informe de incidentes (modal) -->
        <div class="modal fade" id="incidentModal" tabindex="-1" aria-labelledby="incidentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content reservations-modal-content">
                    <div class="modal-header reservations-modal-header">
                        <h5 class="modal-title" id="incidentModalLabel">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Report Incident
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body reservations-modal-body">
                        <div id="incident-error" style="display:none;" class="return-error-msg"></div>
                        <div class="return-form-group">
                            <label for="incident-type">Incident Type <span style="color:#dc2626;">*</span></label>
                            <select id="incident-type" class="return-form-input">
                                <option value="">Select type</option>
                                <option value="accident">Accident</option>
                                <option value="mechanical">Mechanical problem</option>
                                <option value="damage">Damage</option>
                                <option value="warning_light">Warning light</option>
                                <option value="cleanliness">Cleanliness</option>
                                <option value="papers">Papers problem</option>
                                <option value="keys">Keys problem</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="return-form-group">
                            <label for="incident-description">Description <span style="color:#dc2626;">*</span></label>
                            <textarea id="incident-description" class="return-form-input" rows="4"
                                placeholder="Describe the incident in detail (minimum 10 characters)..."></textarea>
                        </div>
                        <div class="return-form-group">
                            <label for="incident-images">Photos <span
                                    style="color:var(--color-gray-400); font-weight:400; text-transform:none; font-size:0.75rem;">(optional,
                                    max 5 images)</span></label>
                            <input type="file" id="incident-images" class="return-form-input" multiple
                                accept="image/jpeg,image/png,image/jpg,image/webp" style="padding:8px;">
                            <div id="incident-images-preview"
                                style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;"></div>
                        </div>
                    </div>
                    <div class="modal-footer reservations-modal-footer">
                        <button type="button" class="btn reservations-btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn reservations-btn-primary" id="submit-incident-btn">
                            <i class="bi bi-exclamation-triangle me-1"></i>Submit Incident
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="incident-success-msg"
            style="display:none; position:fixed; top:24px; left:50%; transform:translateX(-50%); z-index:9999; background:linear-gradient(135deg,#059669,#10b981); color:#fff; padding:14px 32px; border-radius:12px; font-weight:600; box-shadow:0 8px 32px rgba(16,185,129,0.35); font-size:0.95rem; animation:fadeInDown 0.4s ease;">
            <i class="bi bi-check-circle-fill me-2"></i>Incident reported successfully
        </div>

    </div>
</section>

<?php get_footer(); ?>