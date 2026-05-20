(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var contentArea = document.getElementById('checkout-content');
        var emptyState = document.getElementById('checkout-empty');
        var summarySection = document.getElementById('checkout-summary-section');
        var successOverlay = document.getElementById('checkout-success');

        // Elementos de resumen
        var elCarName = document.getElementById('checkout-car-name');
        var elStartDate = document.getElementById('checkout-start-date');
        var elEndDate = document.getElementById('checkout-end-date');
        var elDays = document.getElementById('checkout-days');
        var elPriceDay = document.getElementById('checkout-price-day');
        var elTotal = document.getElementById('checkout-total');

        // Forma de pago
        var payForm = document.getElementById('checkout-pay-form');
        var payBtn = document.getElementById('pay-now');
        var responseDiv = document.getElementById('checkout-response');

        // Recuperar datos de reserva
        var raw = localStorage.getItem('booking_data');
        var booking = null;

        try {
            booking = JSON.parse(raw);
        } catch (e) {
            booking = null;
        }

        // Verificar la validez de los datos (máximo 30 minutos de antigüedad)
        if (!booking || !booking.car_id || !booking.start_date || !booking.end_date) {
            showEmpty();
            return;
        }

        var age = Date.now() - (booking.timestamp || 0);
        if (age > 30 * 60 * 1000) {
            localStorage.removeItem('booking_data');
            showEmpty();
            return;
        }

        // Completar resumen
        if (elCarName) elCarName.textContent = booking.car_title || 'Car #' + booking.car_id;
        if (elStartDate) elStartDate.textContent = formatDate(booking.start_date);
        if (elEndDate) elEndDate.textContent = formatDate(booking.end_date);
        if (elDays) elDays.textContent = booking.days + (booking.days === 1 ? ' day' : ' days');
        if (elPriceDay) elPriceDay.textContent = '$' + parseFloat(booking.price_per_day).toFixed(2) + '/day';
        if (elTotal) elTotal.textContent = '$' + parseFloat(booking.total_price).toFixed(2);

        if (summarySection) summarySection.style.display = '';
        if (emptyState) emptyState.style.display = 'none';

        // Envío del formulario de pago
        if (payForm) {
            payForm.addEventListener('submit', function (e) {
                e.preventDefault();
                clearResponse();

                // Validación básica del lado del cliente de los campos de la tarjeta.
                var cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
                var cardExpiry = document.getElementById('card-expiry').value.trim();
                var cardCvc = document.getElementById('card-cvc').value.trim();

                if (cardNumber.length < 13 || cardNumber.length > 19) {
                    showResponseMsg('Please enter a valid card number.', false);
                    return;
                }

                if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
                    showResponseMsg('Please enter expiry in MM/YY format.', false);
                    return;
                }

                if (!/^\d{3,4}$/.test(cardCvc)) {
                    showResponseMsg('Please enter a valid CVC (3-4 digits).', false);
                    return;
                }

                // Mostrar cargando
                setPayButtonLoading(true);

                // Llamar a un punto final REST de WP existente
                fetch(checkoutData.restUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': checkoutData.nonce,
                    },
                    body: JSON.stringify({
                        car_id: booking.car_id,
                        start_date: booking.start_date,
                        end_date: booking.end_date,
                    }),
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        setPayButtonLoading(false);

                        if (data.success) {
                            // Borrar el almacenamiento local
                            localStorage.removeItem('booking_data');

                            // Mostrar éxito
                            if (contentArea) contentArea.style.display = 'none';
                            if (successOverlay) successOverlay.style.display = '';
                        } else {
                            showResponseMsg(data.message || 'Payment failed. Please try again.', false);
                        }
                    })
                    .catch(function (err) {
                        setPayButtonLoading(false);
                        showResponseMsg('Connection error. Please try again.', false);
                        console.error('Checkout error:', err);
                    });
            });
        }

        // Helpers
        function showEmpty() {
            if (summarySection) summarySection.style.display = 'none';
            if (emptyState) emptyState.style.display = '';
        }

        function formatDate(dateStr) {
            var d = new Date(dateStr + 'T00:00:00');
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            return d.toLocaleDateString('en-US', options);
        }

        function setPayButtonLoading(loading) {
            var textEl = payBtn.querySelector('.checkout-btn-text');
            var loaderEl = payBtn.querySelector('.checkout-btn-loader');

            if (loading) {
                if (textEl) textEl.style.display = 'none';
                if (loaderEl) loaderEl.style.display = '';
                payBtn.disabled = true;
            } else {
                if (textEl) textEl.style.display = '';
                if (loaderEl) loaderEl.style.display = 'none';
                payBtn.disabled = false;
            }
        }

        function showResponseMsg(msg, isSuccess) {
            if (responseDiv) {
                responseDiv.textContent = msg;
                responseDiv.className = 'checkout-response ' + (isSuccess ? 'checkout-success' : 'checkout-error');
            }
        }

        function clearResponse() {
            if (responseDiv) {
                responseDiv.textContent = '';
                responseDiv.className = 'checkout-response';
            }
        }

        // Formato del número de tarjeta (añadir espacios cada 4 dígitos)
        var cardInput = document.getElementById('card-number');
        if (cardInput) {
            cardInput.addEventListener('input', function () {
                var val = this.value.replace(/\D/g, '').substring(0, 16);
                var formatted = val.replace(/(\d{4})(?=\d)/g, '$1 ');
                this.value = formatted;
            });
        }

        // Formato de caducidad
        var expiryInput = document.getElementById('card-expiry');
        if (expiryInput) {
            expiryInput.addEventListener('input', function () {
                var val = this.value.replace(/\D/g, '').substring(0, 4);
                if (val.length >= 2) {
                    val = val.substring(0, 2) + '/' + val.substring(2);
                }
                this.value = val;
            });
        }

        // Límite de CVC
        var cvcInput = document.getElementById('card-cvc');
        if (cvcInput) {
            cvcInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').substring(0, 4);
            });
        }
    });
})();
