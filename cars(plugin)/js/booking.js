/**
 * Booking Modal JS – Rediseño con calendario interactivo y flujo por pasos.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ── DOM refs ── */
        var modal = document.getElementById('booking-modal');
        var overlay = modal ? modal.querySelector('.booking-modal-overlay') : null;
        var closeBtn = modal ? modal.querySelector('.bm-close') : null;
        var carIdField = document.getElementById('booking-car-id');

        if (!modal) return;

        var pricePerDay = (bookingData && bookingData.pricePerDay) ? parseFloat(bookingData.pricePerDay) : 0;
        var isAvailable = false;
        var bookedDates = new Set();
        var AUTH_STORAGE_KEY = 'booking_auth_state';

        /* ── State ── */
        var state = {
            currentStep: 1,
            isAuthenticated: false,
            startDate: null,
            endDate: null,
            hoverDate: null,
            selecting: 'start',
            calYear: 0,
            calMonth: 0,
        };

        /* Referencias de navegación por pasos */
        var steps = modal.querySelectorAll('.bm-step');
        var connectors = modal.querySelectorAll('.bm-step-connector');
        var panels = modal.querySelectorAll('.bm-panel');

        /* Paso 1 */
        var calGrid = document.getElementById('bm-cal-grid');
        var calLabel = document.getElementById('bm-cal-month-label');
        var calPrev = document.getElementById('bm-cal-prev');
        var calNext = document.getElementById('bm-cal-next');
        var startDisp = document.getElementById('bm-start-display');
        var endDisp = document.getElementById('bm-end-display');
        var chipStart = document.getElementById('bm-chip-start');
        var chipEnd = document.getElementById('bm-chip-end');
        var step1Next = document.getElementById('bm-step1-next');
        var availBlock = document.getElementById('bm-availability');
        var availChk = document.getElementById('bm-avail-checking');
        var availOk = document.getElementById('bm-avail-ok');
        var availErr = document.getElementById('bm-avail-error');
        var availErrTxt = document.getElementById('bm-avail-error-text');

        /* Paso 2 */
        var step2Back = document.getElementById('bm-step2-back');
        var step2Next = document.getElementById('bm-step2-next');
        var userNameEl = document.getElementById('bm-user-name');
        var userEmailEl = document.getElementById('bm-user-email');
        var detailStart = document.getElementById('bm-detail-start');
        var detailEnd = document.getElementById('bm-detail-end');
        var detailDays = document.getElementById('bm-detail-days');

        /* Paso 3 (Pago) */
        var step3Back = document.getElementById('bm-step3-back');
        var step3Next = document.getElementById('bm-step3-next');
        var sumCarName = document.getElementById('bm-summary-car-name');
        var sumDatesDays = document.getElementById('bm-sum-dates-days');
        var sumTotal = document.getElementById('bm-sum-total');
        var payBtnText = document.getElementById('bm-pay-btn-text');

        /* Estado de Stripe */
        var stripe = null;
        var elements = null;
        var cardElement = null;
        var cardMounted = false;

        /* Paso 4 (Confirmación) */
        var confirmIcon = document.getElementById('bm-confirm-icon');
        var confirmTitle = document.getElementById('bm-confirm-title');
        var confirmText = document.getElementById('bm-confirm-text');
        var confirmActions = document.getElementById('bm-confirm-actions');
        var btnDone = document.getElementById('bm-btn-done');

        /* MODAL OPEN / CLOSE */
        document.querySelectorAll('.book-car-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var carId = this.getAttribute('data-car-id');
                if (carIdField) carIdField.value = carId;
                openModal();
            });
        });

        function openModal() {
            resetAll();
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            if (bookingData && bookingData.userName) userNameEl.textContent = bookingData.userName;
            if (bookingData && bookingData.userEmail) userEmailEl.textContent = bookingData.userEmail;

            // Restore saved email and auto-focus when Step 0 is shown
            if (!(bookingData && bookingData.isLoggedIn)) {
                var saved = localStorage.getItem(AUTH_STORAGE_KEY);
                if (saved) {
                    try {
                        var data = JSON.parse(saved);
                        var authEmailRestore = document.getElementById('bm-auth-email');
                        if (data.email && authEmailRestore) {
                            authEmailRestore.value = data.email;
                        }
                    } catch (e) {}
                }
                setTimeout(function () {
                    var authEmail = document.getElementById('bm-auth-email');
                    if (authEmail) authEmail.focus();
                }, 50);
            }

            var carId = carIdField ? carIdField.value : '';
            if (carId) {
                fetchBookedDates(carId).then(function (ranges) {
                    bookedDates = new Set(expandRangesToDates(ranges));
                    renderCalendar();
                });
            }
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (overlay) overlay.addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
        });

        /* STEP MANAGEMENT */
        function goToStep(n) {
            state.currentStep = n;
            panels.forEach(function (p) {
                if (n === 0) {
                    p.classList.toggle('active', p.id === 'bm-step-0');
                } else {
                    p.classList.toggle('active', p.id === 'bm-panel-' + n);
                }
            });
            steps.forEach(function (s, i) {
                var stepNum = i + 1;
                s.classList.remove('active', 'completed');
                if (stepNum === n) s.classList.add('active');
                else if (stepNum < n) s.classList.add('completed');
            });
            connectors.forEach(function (c, i) {
                c.classList.toggle('filled', i + 1 < n);
            });
            var content = modal.querySelector('.booking-modal-content');
            if (content) content.scrollTop = 0;
        }

        /* Paso 1 → 2 */
        if (step1Next) step1Next.addEventListener('click', function () {
            if (!state.isAuthenticated) return;
            if (!isAvailable) return;
            fillSummary();
            goToStep(2);
        });

        /* paso 2 → 1 / 3 */
        if (step2Back) step2Back.addEventListener('click', function () { goToStep(1); });
        if (step2Next) step2Next.addEventListener('click', function () {
            fillSummary();
            goToStep(3);
            initStripe();
        });

        /* Paso 3 → 2 / 4 */
        if (step3Back) step3Back.addEventListener('click', function () { goToStep(2); });
        if (step3Next) step3Next.addEventListener('click', function () {
            handlePayment();
        });

        /* Paso 4 reintentar / realizado */
        if (btnDone) btnDone.addEventListener('click', function () {
            closeModal();
            window.location.reload();
        });

        /* BOOKED DATES HELPERS */
        function fetchBookedDates(carId) {
            return fetch('/wp-json/booking/v1/booked-dates', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ car_id: carId })
            })
                .then(function (r) { return r.json(); })
                .catch(function () { return []; });
        }

        function expandRangesToDates(ranges) {
            var dates = [];
            if (!Array.isArray(ranges)) return dates;
            ranges.forEach(function (range) {
                var current = new Date(range.start_date + 'T00:00:00');
                var end = new Date(range.end_date + 'T00:00:00');
                while (current <= end) {
                    var y = current.getFullYear();
                    var m = String(current.getMonth() + 1).padStart(2, '0');
                    var dd = String(current.getDate()).padStart(2, '0');
                    dates.push(y + '-' + m + '-' + dd);
                    current.setDate(current.getDate() + 1);
                }
            });
            return dates;
        }

        /* CALENDAR */
        var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        function initCalendar() {
            var now = new Date();
            state.calYear = now.getFullYear();
            state.calMonth = now.getMonth();
            renderCalendar();
        }

        function renderCalendar() {
            var year = state.calYear;
            var month = state.calMonth;
            calLabel.textContent = MONTHS[month] + ' ' + year;

            var firstDay = new Date(year, month, 1).getDay();
            var daysInMonth = new Date(year, month + 1, 0).getDate();

            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var todayNormalized = new Date(today.getTime());

            calGrid.innerHTML = '';

            for (var e = 0; e < firstDay; e++) {
                var empty = document.createElement('div');
                empty.className = 'bm-day bm-day-empty';
                calGrid.appendChild(empty);
            }

            for (var d = 1; d <= daysInMonth; d++) {
                var dayDate = new Date(year, month, d);
                dayDate.setHours(0, 0, 0, 0);

                var cell = document.createElement('div');
                cell.className = 'bm-day';
                cell.textContent = d;
                cell.setAttribute('data-ts', dayDate.getTime());

                var isPast = dayDate < todayNormalized;

                if (isPast) {
                    cell.classList.add('bm-day-past');
                } else {
                    var dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                    cell.setAttribute('data-date', dateStr);
                    var isBlocked = bookedDates.has(dateStr);

                    if (dayDate.getTime() === todayNormalized.getTime()) cell.classList.add('bm-day-today');

                    if (isBlocked) {
                        cell.classList.add('bm-day-disabled');
                    } else {
                        applyCellRangeClasses(cell, dayDate);
                        cell.addEventListener('click', makeDayClick(dayDate));
                        cell.addEventListener('mouseenter', makeDayHover(dayDate));
                    }
                }

                calGrid.appendChild(cell);
            }

            // Limpiar hover cuando el ratón sale del grid entero
            calGrid.addEventListener('mouseleave', function () {
                if (state.hoverDate) {
                    state.hoverDate = null;
                    updateRangeClasses();
                }
            }, { once: true });
        }

        /* Actualiza solo las clases de rango en las celdas existentes (sin re-render) */
        function updateRangeClasses() {
            var cells = calGrid.querySelectorAll('.bm-day');
            cells.forEach(function (cell) {
                if (cell.classList.contains('bm-day-empty') ||
                    cell.classList.contains('bm-day-past')) return;

                var ts = parseInt(cell.getAttribute('data-ts'), 10);
                if (!ts) return;

                var d = new Date(ts);
                cell.classList.remove('bm-day-start', 'bm-day-end', 'bm-day-in-range');
                applyCellRangeClasses(cell, d);
            });
        }

        function applyCellRangeClasses(cell, dayDate) {
            var start = state.startDate;
            var end = state.endDate;
            var hover = state.hoverDate;
            var t = dayDate.getTime();

            var isStart = start && t === start.getTime();
            var isEnd = end && t === end.getTime();

            // Determinar el final del alcance efectivo
            var rangeEnd = end;
            if (!end && start && hover && hover > start) rangeEnd = hover;
            else if (!end && start && hover && hover < start) {
                rangeEnd = start;
                start = hover;
            }

            var inRange = start && rangeEnd && t > start.getTime() && t < rangeEnd.getTime();
            var isHoverEnd = !end && state.hoverDate && t === state.hoverDate.getTime() && state.startDate;

            if (isStart) cell.classList.add('bm-day-start');
            if (isEnd || isHoverEnd) cell.classList.add('bm-day-end');
            if (inRange || isStart || isEnd || isHoverEnd) cell.classList.add('bm-day-in-range');
        }

        function makeDayClick(date) {
            return function () {
                if (state.selecting === 'start' || (state.startDate && state.endDate)) {
                    // start fresh selection
                    state.startDate = date;
                    state.endDate = null;
                    state.selecting = 'end';
                    isAvailable = false;
                    step1Next.disabled = true;
                    hideAvail();
                } else {
                    // selecting end
                    if (date <= state.startDate) {
                        // clicked before start → restart
                        state.startDate = date;
                        state.endDate = null;
                        state.selecting = 'end';
                        isAvailable = false;
                        step1Next.disabled = true;
                        hideAvail();
                    } else {
                        state.endDate = date;
                        state.selecting = 'start';
                        onRangeSelected();
                    }
                }
                updateChips();
                renderCalendar();
            };
        }

        function makeDayHover(date) {
            return function () {
                if (state.selecting === 'end' && state.startDate && !state.endDate) {
                    state.hoverDate = date;
                    updateRangeClasses();
                }
            };
        }

        function updateChips() {
            if (state.startDate) {
                startDisp.textContent = formatDate(state.startDate);
                startDisp.classList.add('has-date');
            } else {
                startDisp.textContent = 'Select date';
                startDisp.classList.remove('has-date');
            }
            if (state.endDate) {
                endDisp.textContent = formatDate(state.endDate);
                endDisp.classList.add('has-date');
            } else {
                endDisp.textContent = 'Select date';
                endDisp.classList.remove('has-date');
            }
        }

        function formatDate(d) {
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function formatDateISO(d) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + dd;
        }

        /* Month navigation */
        if (calPrev) calPrev.addEventListener('click', function () {
            var d = new Date(state.calYear, state.calMonth - 1, 1);
            state.calYear = d.getFullYear();
            state.calMonth = d.getMonth();
            renderCalendar();
        });

        if (calNext) calNext.addEventListener('click', function () {
            var d = new Date(state.calYear, state.calMonth + 1, 1);
            state.calYear = d.getFullYear();
            state.calMonth = d.getMonth();
            renderCalendar();
        });

        /*  AVAILABILITY CHECK */
        var availabilityTimeout;

        function triggerAvailabilityCheck(carId, startDate, endDate) {
            clearTimeout(availabilityTimeout);
            availabilityTimeout = setTimeout(function () {
                checkAvailability(carId, startDate, endDate);
            }, 400);
        }

        function onRangeSelected() {
            if (!state.startDate || !state.endDate) return;

            // Mantener bloqueado hasta que Laravel confirme
            isAvailable = false;
            step1Next.disabled = true;

            // Si no hay carId, omitir el check
            var carId = carIdField ? carIdField.value : '';
            if (!carId) {
                showAvail('ok');
                return;
            }

            triggerAvailabilityCheck(
                carId,
                formatDateISO(state.startDate),
                formatDateISO(state.endDate)
            );
        }

        function checkAvailability(carId, startDate, endDate) {
            showAvail('checking');

            var url = '/wp-json/booking/v1/availability';

            var payload = {
                car_id: carId,
                start_date: startDate,
                end_date: endDate
            };

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': bookingData ? bookingData.nonce : ''
                },
                body: JSON.stringify(payload)
            })
                .then(function (r) {
                    if (!r.ok) {
                        throw new Error('Server error');
                    }
                    return r.json();
                })
                .then(function (data) {
                    if (data.available) {
                        isAvailable = true;
                        step1Next.disabled = false;
                        showAvail('ok');
                        // Actualizar precio con dato real de Laravel
                        if (data.total_price && data.days) {
                            pricePerDay = parseFloat(data.total_price) / parseInt(data.days);
                        }
                    } else {
                        isAvailable = false;
                        step1Next.disabled = true;
                        showAvail('error', data.message || 'Fechas no disponibles.');
                    }
                })
                .catch(function (err) {
                    console.warn('Availability check failed:', err);
                    isAvailable = false;
                    step1Next.disabled = true;
                    showAvail('error', 'Fallo de conexión. Por favor reintenta.');
                });
        }

        function showAvail(state, msg) {
            availBlock.style.display = 'block';
            availChk.style.display = 'none';
            availOk.style.display = 'none';
            availErr.style.display = 'none';
            if (state === 'checking') availChk.style.display = 'flex';
            else if (state === 'ok') availOk.style.display = 'flex';
            else if (state === 'error') {
                availErrTxt.textContent = msg || 'Not available';
                availErr.style.display = 'flex';
            }
        }

        function hideAvail() {
            availBlock.style.display = 'none';
            availChk.style.display = 'none';
            availOk.style.display = 'none';
            availErr.style.display = 'none';
        }

        /* RESUMEN Y PAGO (Paso 3)) */
        function fillSummary() {
            var days = Math.ceil((state.endDate - state.startDate) / 86400000);
            var total = days * pricePerDay;
            var title = (bookingData && bookingData.carTitle) ? bookingData.carTitle : 'Vehicle';

            // Paso 2: vista previa del viaje
            if (detailStart) detailStart.textContent = formatDate(state.startDate);
            if (detailEnd) detailEnd.textContent = formatDate(state.endDate);
            if (detailDays) detailDays.textContent = days + (days === 1 ? ' day' : ' days');

            // Paso 3 – Resumen de pago
            var dsStr = formatDate(state.startDate) + ' – ' + formatDate(state.endDate) + ' (' + days + (days === 1 ? ' day' : ' days') + ')';
            var totalStr = '$' + total.toFixed(2);

            if (sumCarName) sumCarName.textContent = title;
            if (sumDatesDays) sumDatesDays.textContent = dsStr;
            if (sumTotal) sumTotal.textContent = totalStr;
            if (payBtnText) payBtnText.textContent = 'Pay ' + totalStr;
        }

        /* FLUJO DE PAGOS Y STRIPE */

        function initStripe() {
            if (stripe || !bookingData.stripePublicKey) return;

            stripe = Stripe(bookingData.stripePublicKey);
            elements = stripe.elements({
                fonts: [{ cssSrc: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap' }],
            });

            if (!cardElement) {
                cardElement = elements.create('card', {
                    hidePostalCode: true,
                    style: {
                        base: {
                            fontFamily: 'Inter, sans-serif',
                            fontSize: '15px',
                            color: '#ffffff',
                            '::placeholder': { color: '#cbd5e0' },
                            iconColor: '#ffffff',
                        },
                        invalid: { color: '#e53e3e', iconColor: '#e53e3e' },
                    },
                });
            }

            if (!cardMounted) {
                cardElement.mount('#bm-stripe-card-element');
                cardMounted = true;

                cardElement.on('change', function (event) {
                    var errorDiv = document.getElementById('bm-stripe-card-errors');
                    if (errorDiv) errorDiv.textContent = event.error ? event.error.message : '';
                    if (step3Next) step3Next.disabled = !event.complete;
                });
            }
        }

        async function handlePayment() {
            var errorDiv = document.getElementById('bm-stripe-card-errors');
            if (errorDiv) errorDiv.textContent = '';

            // Validar titular de la tarjeta
            var cardholderInput = document.getElementById('bm-cardholder');
            var cardholderName = cardholderInput ? cardholderInput.value.trim() : '';

            if (!cardholderName) {
                if (errorDiv) errorDiv.textContent = 'Por favor, introduce el nombre del titular de la tarjeta.';
                if (cardholderInput) {
                    cardholderInput.style.borderColor = '#e53e3e';
                    cardholderInput.focus();
                }
                return;
            } else if (cardholderInput) {
                cardholderInput.style.borderColor = '';
            }

            if (step3Next) step3Next.disabled = true;
            if (payBtnText) payBtnText.textContent = 'Processing...';

            var days = Math.ceil((state.endDate - state.startDate) / 86400000);
            var amount = Math.round(days * pricePerDay * 100); // amount in cents

            if (!amount || amount <= 0) {
                if (errorDiv) errorDiv.textContent = 'Invalid amount. Please re-select dates.';
                if (step3Next) step3Next.disabled = false;
                return;
            }

            try {
                // Crear intención de pago
                var carId = carIdField ? carIdField.value : '';
                var formData = new URLSearchParams({
                    action: 'bm_create_payment_intent',
                    nonce: bookingData.ajaxNonce,
                    amount: amount,
                    car_id: carId,
                    start_date: formatDateISO(state.startDate),
                    end_date: formatDateISO(state.endDate),
                    car_name: bookingData.carTitle
                });

                var res = await fetch(bookingData.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString(),
                });

                var json = await res.json();
                if (!json.success) {
                    throw new Error(json.data?.message || 'Failed to initialize payment.');
                }

                var clientSecret = json.data.clientSecret;

                // Confirmar pago
                var cardholderName = document.getElementById('bm-cardholder')?.value?.trim() || bookingData.userName;
                var userEmail = bookingData.userEmail;

                var result = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: cardholderName, email: userEmail },
                    },
                });

                if (result.error) {
                    throw new Error(result.error.message);
                }

                if (result.paymentIntent.status === 'succeeded') {
                    // Finalizar reserva
                    goToStep(4);
                    submitBooking(result.paymentIntent.id);
                }

            } catch (err) {
                console.error('[Booking] Payment error:', err);
                if (errorDiv) errorDiv.textContent = err.message;
                if (step3Next) step3Next.disabled = false;
                if (payBtnText) {
                    var total = (days * pricePerDay);
                    payBtnText.textContent = 'Pay $' + total.toFixed(2);
                }
            }
        }

        /* ENVÍO DE RESERVA (paso 4) */
        function submitBooking(paymentIntentId) {
            // Reset step 4 UI to loading state
            confirmIcon.className = 'bm-confirm-icon';
            confirmIcon.innerHTML = '<div class="bm-spinner bm-spinner-lg"></div>';
            confirmTitle.textContent = 'Processing Payment…';
            confirmText.textContent = 'Please wait while we confirm your reservation privately.';
            confirmActions.style.display = 'none';

            var carId = carIdField ? carIdField.value : '';
            var startDate = formatDateISO(state.startDate);
            var endDate = formatDateISO(state.endDate);

            // Solicitud de obtención de datos hacia el punto final REST de WordPress
            var restUrl = (bookingData && bookingData.restUrl) ? bookingData.restUrl : '/wp-json/booking/v1/reserve';

            var payload = {
                car_id: carId,
                start_date: startDate,
                end_date: endDate,
                payment_intent_id: paymentIntentId || ''
            };

            fetch(restUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': bookingData ? bookingData.nonce : ''
                },
                body: JSON.stringify(payload)
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        // 409 = conflicto de disponibilidad → volver a Step 1
                        if (res.status === 409) {
                            isAvailable = false;
                            step1Next.disabled = true;
                            showAvail('error', 'Este coche ya no está disponible para las fechas seleccionadas');
                            goToStep(1);
                            return;
                        }
                        if (!res.ok) throw new Error(data.message || 'Payment processing failed.');
                        return data;
                    });
                })
                .then(function (data) {
                    if (data) showSuccess();
                })
                .catch(function (err) {
                    showError(err.message || 'Could not process the payment.');
                });
        }

        function showSuccess() {
            confirmIcon.className = 'bm-confirm-icon success';
            confirmIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
            confirmTitle.textContent = 'Payment Successful!';
            confirmText.textContent = 'Your reservation has been confirmed. You will receive an email shortly.';
            confirmActions.style.display = 'flex';
        }

        function showError(msg) {
            confirmIcon.className = 'bm-confirm-icon error';
            confirmIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
            confirmTitle.textContent = 'Payment Failed';
            confirmText.textContent = msg + ' Please close and try again, or check your connection.';
            // En error el botón "done" cerrará el modal sin recargar
            confirmActions.style.display = 'flex';
        }

        /* RESET */
        function resetAll() {
            state.startDate = null;
            state.endDate = null;
            state.hoverDate = null;
            state.selecting = 'start';
            isAvailable = false;
            if (step1Next) step1Next.disabled = true;
            hideAvail();
            updateChips();
            if (bookingData && bookingData.isLoggedIn) {
                state.isAuthenticated = true;
                goToStep(1);
            } else {
                state.isAuthenticated = false;
                goToStep(0);
            }
            initCalendar();
        }

        //  LÓGICA AUTH STEP 0
        var emailInput = document.getElementById('bm-auth-email');
        var extraBlock = document.getElementById('bm-auth-extra');
        var btn = document.getElementById('bm-auth-continue');
        var errorBox = document.getElementById('bm-auth-error');

        if (btn) {
            var currentMode = 'email'; // email | login | register

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function getAuthButtonText() {
                if (currentMode === 'login') return 'Login';
                if (currentMode === 'register') return 'Create account and continue';
                return 'Continue';
            }

            function setAuthLoading(isLoading, text) {
                btn.disabled = isLoading;
                btn.textContent = isLoading ? (text || 'Loading...') : getAuthButtonText();
            }

            emailInput.addEventListener('input', function () {
                currentMode = 'email';
                extraBlock.innerHTML = '';
                if (errorBox) {
                    errorBox.style.display = 'none';
                    errorBox.textContent = '';
                }
                if (btn) {
                    btn.textContent = 'Continue';
                    btn.disabled = false;
                }
                localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
                    email: emailInput.value || ''
                }));
            });

            btn.addEventListener('click', function () {
                if (btn.disabled) return;

                errorBox.style.display = 'none';
                var email = emailInput.value.trim();

                if (!email) {
                    showAuthError('Please enter your email.');
                    return;
                }

                if (!isValidEmail(email)) {
                    showAuthError('Please enter a valid email address.');
                    return;
                }

                // Comprobar usuario
                if (currentMode === 'email') {
                    setAuthLoading(true, 'Checking...');
                    fetch('/wp-json/booking/v1/check-user', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': bookingData ? bookingData.nonce : '' },
                        body: JSON.stringify({ email: email })
                    })
                        .then(r => r.json())
                        .then(function (data) {
                            extraBlock.innerHTML = '';
                            if (data.exists) {
                                currentMode = 'login';
                                setAuthLoading(false);
                                extraBlock.innerHTML = `
                                <div class="bm-field-group">
                                  <label class="bm-label">Password</label>
                                  <input type="password" id="bm-auth-password" class="bm-input" placeholder="Enter your password" autocomplete="current-password">
                                </div>
                            `;
                                setTimeout(function () {
                                    var pwField = document.getElementById('bm-auth-password');
                                    if (pwField) pwField.focus();
                                }, 50);
                            } else {
                                currentMode = 'register';
                                setAuthLoading(false);
                                extraBlock.innerHTML = `
                                <div class="bm-field-group">
                                  <label class="bm-label">Full Name</label>
                                  <input type="text" id="bm-auth-name" class="bm-input" placeholder="Your full name" autocomplete="name">
                                </div>
                            `;
                                setTimeout(function () {
                                    var nameField = document.getElementById('bm-auth-name');
                                    if (nameField) nameField.focus();
                                }, 50);
                            }
                        })
                        .catch(function () {
                            setAuthLoading(false);
                            showAuthError('Connection error. Please try again.');
                        });
                    return;
                }

                // Login
                if (currentMode === 'login') {
                    var password = document.getElementById('bm-auth-password').value;
                    if (!password) {
                        showAuthError('Please enter your password.');
                        return;
                    }
                    setAuthLoading(true, 'Logging in...');
                    authUser({ email: email, password: password });
                }

                // Register
                if (currentMode === 'register') {
                    var name = document.getElementById('bm-auth-name').value;
                    if (!name) {
                        showAuthError('Please enter your name.');
                        return;
                    }
                    setAuthLoading(true, 'Creating account...');
                    authUser({ email: email, name: name });
                }
            });

            function authUser(payload) {
                fetch('/wp-json/booking/v1/auth-user', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': bookingData ? bookingData.nonce : '' },
                    body: JSON.stringify(payload)
                })
                    .then(r => r.json())
                    .then(function (res) {
                        if (!res.success) {
                            setAuthLoading(false);
                            var msg = (res.message) ? res.message : 'Incorrect email or password.';
                            showAuthError(msg);
                            return;
                        }

                        // ACTUALIZAR bookingData
                        state.isAuthenticated = true;

                        if (window.bookingData) {
                            bookingData.userName = res.user.name;
                            bookingData.userEmail = res.user.email;
                            bookingData.isLoggedIn = true;
                        }

                        if (userNameEl) userNameEl.textContent = res.user.name;
                        if (userEmailEl) userEmailEl.textContent = res.user.email;

                        // Limpiar persistencia del Step 0
                        localStorage.removeItem(AUTH_STORAGE_KEY);

                        // Update nonces for subsequent authenticated calls
                        if (res.nonce && window.bookingData) {
                            bookingData.nonce = res.nonce;
                        }
                        if (res.ajaxNonce && window.bookingData) {
                            bookingData.ajaxNonce = res.ajaxNonce;
                        }

                        // Re-fetch booked dates now that user is authenticated
                        var carId = carIdField ? carIdField.value : '';
                        if (carId) {
                            fetchBookedDates(carId).then(function (ranges) {
                                bookedDates = new Set(expandRangesToDates(ranges));
                                renderCalendar();
                            });
                        }

                        // Pasar al step real
                        goToStep(1);
                    })
                    .catch(function () {
                        setAuthLoading(false);
                        showAuthError('Connection error. Please try again.');
                    });
            }

            function showAuthError(msg) {
                errorBox.textContent = msg;
                errorBox.style.display = 'block';
            }
        }

    });
})();
