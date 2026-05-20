<?php
/**
 * Formulario de reserva HTML – Rediseño con calendario interactivo y pasos.
 * Renderizado via wp_footer con hook en las páginas de single-cars.
 */

defined('ABSPATH') or die('No direct access.');

// Clave pública de Stripe — leída desde wp-config.php
// En wp-config.php añade:
//   define( 'STRIPE_PUBLIC_KEY', 'pk_test_...' );
//   define( 'STRIPE_SECRET_KEY', 'sk_test_...' );
// NO redefinimos aquí para no pisar el valor de wp-config.php.

function booking_render_modal()
{
    if (!is_singular('cars')) {
        return;
    }

    // Debug: confirma en wp-content/debug.log si la clave llega vacía
    if (!defined('STRIPE_PUBLIC_KEY') || empty(STRIPE_PUBLIC_KEY)) {
        error_log('[BookingModal] ERROR: STRIPE_PUBLIC_KEY no está definida o está vacía en wp-config.php.');
    }
    ?>

    <div id="booking-modal" class="booking-modal" aria-hidden="true" role="dialog" aria-labelledby="booking-modal-title">
        <div class="booking-modal-overlay"></div>
        <div class="booking-modal-content">

            <!-- Campos ocultos -->
            <input type="hidden" id="booking-car-id"    value="">
            <input type="hidden" id="booking-price-day" value="0"> <!-- precio/día en céntimos -->

            <!-- Header -->
            <div class="bm-header">
                <div class="bm-header-left">
                    <div class="bm-header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8"  y1="2" x2="8"  y2="6"/>
                            <line x1="3"  y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="booking-modal-title" class="bm-title">Reserve Your Car</h3>
                        <p class="bm-subtitle">Complete the steps below to book</p>
                    </div>
                </div>
                <button type="button" class="bm-close" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6"  x2="6"  y2="18"/>
                        <line x1="6"  y1="6"  x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Steps indicator -->
            <div class="bm-steps" id="bm-steps">

                <div class="bm-step active" data-step="1">
                    <div class="bm-step-circle">
                        <span class="bm-step-num">1</span>
                        <svg class="bm-step-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="bm-step-label">Dates</span>
                </div>

                <div class="bm-step-connector"></div>

                <div class="bm-step" data-step="2">
                    <div class="bm-step-circle">
                        <span class="bm-step-num">2</span>
                        <svg class="bm-step-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="bm-step-label">Details</span>
                </div>

                <div class="bm-step-connector"></div>

                <div class="bm-step" data-step="3">
                    <div class="bm-step-circle">
                        <span class="bm-step-num">3</span>
                        <svg class="bm-step-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="bm-step-label">Payment</span>
                </div>

                <div class="bm-step-connector"></div>

                <div class="bm-step" data-step="4">
                    <div class="bm-step-circle">
                        <span class="bm-step-num">4</span>
                        <svg class="bm-step-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="bm-step-label">Confirm</span>
                </div>

            </div>

            <!-- Contenido de los pasos-->
            <div class="bm-body">

                <!-- Paso 0 – Autenticación -->
                <div class="bm-panel" id="bm-step-0">

                    <div class="bm-info-banner" style="margin-bottom: 22px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Sign in or create a free account to continue</span>
                    </div>

                    <div class="bm-field-group">
                        <label class="bm-label" for="bm-auth-email">Email Address</label>
                        <input type="email" id="bm-auth-email" class="bm-input"
                               placeholder="your@email.com" required autocomplete="email">
                    </div>

                    <div id="bm-auth-extra"></div>

                    <div id="bm-auth-error" class="bm-auth-error" style="display:none;"></div>

                    <div class="bm-panel-footer" style="margin-top: 20px;">
                        <div></div>
                        <button type="button" id="bm-auth-continue" class="bm-btn-primary">
                            Continue
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </div>

                </div>

                <!-- Paso 1 – Fechas -->
                <div class="bm-panel active" id="bm-panel-1">

                    <div class="bm-calendar-header">
                        <button type="button" class="bm-cal-nav" id="bm-cal-prev" aria-label="Previous month">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                        </button>
                        <span class="bm-cal-month-label" id="bm-cal-month-label"></span>
                        <button type="button" class="bm-cal-nav" id="bm-cal-next" aria-label="Next month">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bm-calendar-weekdays">
                        <span>Sun</span><span>Mon</span><span>Tue</span>
                        <span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                    </div>
                    <div class="bm-calendar-grid" id="bm-cal-grid"></div>

                    <!-- Check-in / Check-out -->
                    <div class="bm-date-range-display" id="bm-date-range-display">
                        <div class="bm-date-chip" id="bm-chip-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <span class="bm-chip-label">Check-in</span>
                                <span class="bm-chip-date" id="bm-start-display">Select date</span>
                            </div>
                        </div>
                        <div class="bm-range-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </div>
                        <div class="bm-date-chip" id="bm-chip-end">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <span class="bm-chip-label">Check-out</span>
                                <span class="bm-chip-date" id="bm-end-display">Select date</span>
                            </div>
                        </div>
                    </div>

                    <!-- Indicador de disponibilidad -->
                    <div id="bm-availability" class="bm-availability" style="display:none;">
                        <div id="bm-avail-checking" class="bm-avail-state bm-avail-checking" style="display:none;">
                            <div class="bm-spinner"></div>
                            <span>Checking availability…</span>
                        </div>
                        <div id="bm-avail-ok" class="bm-avail-state bm-avail-ok" style="display:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Available for selected dates</span>
                        </div>
                        <div id="bm-avail-error" class="bm-avail-state bm-avail-error" style="display:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8"  x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span id="bm-avail-error-text">Not available</span>
                        </div>
                    </div>

                    <div class="bm-panel-footer">
                        <div></div>
                        <button type="button" class="bm-btn-primary" id="bm-step1-next" disabled>
                            Continue
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Paso 2 – Detalles -->
                <div class="bm-panel" id="bm-panel-2">

                    <div class="bm-trip-preview">
                        <div class="bm-trip-preview-item">
                            <span class="bm-trip-preview-label">Check-in</span>
                            <span class="bm-trip-preview-val" id="bm-detail-start">—</span>
                        </div>
                        <div class="bm-trip-preview-sep"></div>
                        <div class="bm-trip-preview-item">
                            <span class="bm-trip-preview-label">Duration</span>
                            <span class="bm-trip-preview-val" id="bm-detail-days">— days</span>
                        </div>
                        <div class="bm-trip-preview-sep"></div>
                        <div class="bm-trip-preview-item">
                            <span class="bm-trip-preview-label">Check-out</span>
                            <span class="bm-trip-preview-val" id="bm-detail-end">—</span>
                        </div>
                    </div>

                    <div class="bm-info-banner">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8"  x2="12.01" y2="8"/>
                        </svg>
                        <span>Your account info is filled in automatically</span>
                    </div>

                    <div class="bm-field-group">
                        <label class="bm-label">Full Name</label>
                        <div class="bm-field-readonly" id="bm-user-name">—</div>
                    </div>

                    <div class="bm-field-group">
                        <label class="bm-label">Email Address</label>
                        <div class="bm-field-readonly" id="bm-user-email">—</div>
                    </div>

                    <div class="bm-section-divider"></div>

                    <div class="bm-field-group">
                        <label class="bm-label">
                            Additional Notes
                            <span class="bm-optional">(optional)</span>
                        </label>
                        <textarea class="bm-textarea" id="bm-notes" rows="3"
                            placeholder="Special requests, pick-up location…"></textarea>
                    </div>

                    <div class="bm-panel-footer">
                        <button type="button" class="bm-btn-secondary" id="bm-step2-back">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="19" y1="12" x2="5"  y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            Back
                        </button>
                        <button type="button" class="bm-btn-primary" id="bm-step2-next">
                            Review Summary
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Paso 3 – Pago (Stripe.js) -->
                <div class="bm-panel" id="bm-panel-3">

                    <div class="bm-summary-card" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0; border-bottom: none;">
                        <div class="bm-summary-car-header">
                            <span class="bm-summary-car" id="bm-summary-car-name">—</span>
                            <span class="bm-summary-badge">Booking Summary</span>
                        </div>
                        <div class="bm-summary-lines">
                            <div class="bm-summary-line">
                                <span>Duration</span>
                                <span id="bm-sum-dates-days">—</span>
                            </div>
                            <div class="bm-summary-line bm-summary-total">
                                <span>Total to pay</span>
                                <span id="bm-sum-total">€0</span>
                            </div>
                        </div>
                    </div>

                    <div class="bm-payment-form">
                        <div class="bm-payment-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Payment Details
                        </div>

                        <div class="bm-field-group">
                            <label class="bm-label" for="bm-cardholder">Cardholder Name</label>
                            <input type="text" class="bm-input" id="bm-cardholder"
                                   placeholder="e.g. John Doe" autocomplete="cc-name" />
                        </div>

                        <div class="bm-field-group">
                            <label class="bm-label">Card Details</label>
                            <div id="bm-stripe-card-element" class="bm-stripe-element">
                                <!-- Stripe.js se monta aquí -->
                            </div>
                            <div id="bm-stripe-card-errors" class="bm-stripe-errors" role="alert"></div>
                        </div>


                    </div>

                    <p class="bm-summary-note">
                        By proceeding you accept our rental terms &amp; conditions.
                        This is a secure 256-bit encrypted connection powered by
                        <strong>Stripe</strong>.
                    </p>

                    <div class="bm-panel-footer">
                        <button type="button" class="bm-btn-secondary" id="bm-step3-back">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="19" y1="12" x2="5"  y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            Back
                        </button>
                        <button type="button" class="bm-btn-primary" id="bm-step3-next" disabled>
                            <span id="bm-pay-btn-text">Pay Now</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="12" y1="2" x2="12" y2="22"/>
                                <polyline points="19 12 12 19 5 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 4 – Confirmación -->
                <div class="bm-panel" id="bm-panel-4">
                    <div class="bm-confirm-body">

                        <div class="bm-confirm-icon" id="bm-confirm-icon">
                            <div class="bm-spinner bm-spinner-lg"></div>
                        </div>

                        <h4 class="bm-confirm-title" id="bm-confirm-title">Processing Payment…</h4>
                        <p  class="bm-confirm-text"  id="bm-confirm-text">
                            Please wait while we confirm your reservation.
                        </p>

                        <div id="bm-confirm-actions" style="display:none;" class="bm-confirm-actions">
                            <button type="button" class="bm-btn-primary" id="bm-btn-done">
                                Done
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </button>
                        </div>

                        <div id="bm-confirm-error-actions" style="display:none;" class="bm-confirm-actions">
                            <button type="button" class="bm-btn-secondary" id="bm-retry-back">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <line x1="19" y1="12" x2="5"  y2="12"/>
                                    <polyline points="12 19 5 12 12 5"/>
                                </svg>
                                Go Back
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
    .bm-stripe-element {
        padding: 12px 14px;
        border: 1px solid var(--bm-border, #e2e8f0);
        border-radius: 8px;
        background: var(--bm-input-bg, #fff);
        transition: border-color 0.2s;
        cursor: text;
    }
    .bm-stripe-element--focus   { border-color: var(--bm-accent, #635bff); outline: none; }
    .bm-stripe-element--invalid { border-color: #e53e3e; }
    .bm-stripe-errors   { min-height: 16px; margin-top: 6px; font-size: 0.78rem; color: #e53e3e; }

    </style>

    <?php
}
add_action('wp_footer', 'booking_render_modal');