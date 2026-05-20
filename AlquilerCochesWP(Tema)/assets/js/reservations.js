document.addEventListener('DOMContentLoaded', function () {
    const gridEl = document.getElementById('reservations-grid');
    const loadingEl = document.getElementById('reservations-loading');
    const emptyEl = document.getElementById('reservations-empty');

    let allReservations = [];
    let cancelId = null;

    // Cargamos las Reservas
    function loadReservations() {
        loadingEl.style.display = 'flex';
        gridEl.style.display = 'none';
        emptyEl.style.display = 'none';

        fetch('/wp-json/booking/v1/my-reservations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': neuvoData.nonce
            }
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Server error');
                return res.json();
            })
            .then(function (data) {
                var reservations = data.data || [];

                allReservations = reservations.map(function (r) {
                    var startDate = r.start_date ? r.start_date.split('T')[0] : '';
                    var endDate = r.end_date ? r.end_date.split('T')[0] : '';
                    var createdAt = r.created_at || '';

                    return {
                        id: r.id,
                        car: { name: r.car ? r.car.name : 'Vehicle' },
                        car_id: r.car_id,
                        start_date: startDate,
                        end_date: endDate,
                        status: r.status || 'confirmed',
                        total_price: parseFloat(r.total_price || 0).toFixed(2),
                        created_at: createdAt
                    };
                });

                updateStats();

                renderReservations(allReservations);
            })
            .catch(function (err) {
                console.error('Error loading reservations:', err);
                loadingEl.style.display = 'none';
                gridEl.style.display = 'none';
                emptyEl.querySelector('h3').textContent = 'Connection Error';
                emptyEl.querySelector('p').textContent = 'Could not load your reservations. Please try again later.';
                emptyEl.style.display = 'flex';
            });
    }



    // Actualizar estadísticas
    function updateStats() {
        document.getElementById('stat-total').textContent = allReservations.length;
        document.getElementById('stat-confirmed').textContent = allReservations.filter(r => r.status === 'confirmed').length;
        document.getElementById('stat-completed').textContent = allReservations.filter(r => r.status === 'completed').length;
        document.getElementById('stat-cancelled').textContent = allReservations.filter(r => r.status === 'cancelled').length;
    }

    // Renderizar tarjetas de reserva
    function renderReservations(reservations) {
        loadingEl.style.display = 'none';

        if (reservations.length === 0) {
            gridEl.style.display = 'none';
            emptyEl.querySelector('h3').textContent = 'No Reservations Yet';
            emptyEl.querySelector('p').textContent = "You haven't made any reservations yet. Explore our fleet and book your dream car today!";
            emptyEl.style.display = 'flex';
            return;
        }

        emptyEl.style.display = 'none';
        gridEl.style.display = 'grid';
        gridEl.innerHTML = '';

        reservations.forEach((r, index) => {
            const carName = r.car ? r.car.name : ('Car #' + r.car_id);
            const startDate = formatDate(r.start_date);
            const endDate = formatDate(r.end_date);
            const days = dateDiffDays(r.start_date, r.end_date);
            const statusClass = getStatusClass(r.status);
            const statusIcon = getStatusIcon(r.status);
            const bookedOn = r.created_at ? formatDate(r.created_at.split('T')[0]) : '—';
            const totalPrice = r.total_price ? r.total_price + ' €' : '';

            const card = document.createElement('div');
            card.className = 'reservation-card';
            card.setAttribute('data-status', r.status);
            card.style.animationDelay = (index * 0.08) + 's';

            card.innerHTML = `
            <div class="reservation-card-header">
                <div class="reservation-status ${statusClass}">
                    <i class="bi ${statusIcon}"></i> ${capitalize(r.status)}
                </div>
                <span class="reservation-id">#${r.id}</span>
            </div>
            <div class="reservation-card-body">
                <h3 class="reservation-car-name"><i class="bi bi-car-front-fill me-2"></i>${escapeHtml(carName)}</h3>
                <div class="reservation-dates">
                    <div class="reservation-date-item">
                        <span class="reservation-date-label"><i class="bi bi-calendar-event me-1"></i>Pick-up</span>
                        <span class="reservation-date-value">${startDate}</span>
                    </div>
                    <div class="reservation-date-divider">
                        <i class="bi bi-arrow-right"></i>
                        <span class="reservation-duration">${days} day${days !== 1 ? 's' : ''}</span>
                    </div>
                    <div class="reservation-date-item">
                        <span class="reservation-date-label"><i class="bi bi-calendar-check me-1"></i>Drop-off</span>
                        <span class="reservation-date-value">${endDate}</span>
                    </div>
                </div>
                <div class="reservation-meta">
                    <span><i class="bi bi-clock-history me-1"></i>Booked: ${bookedOn}</span>
                    ${totalPrice ? '<span><i class="bi bi-currency-euro me-1"></i>Total: ' + totalPrice + '</span>' : ''}
                </div>
            </div>
            ${(r.status === 'confirmed' || r.status === 'active') ? `
            <div class="reservation-card-footer">
                <button class="reservation-cancel-btn" data-id="${r.id}" data-car="${escapeHtml(carName)}" data-start="${startDate}" data-end="${endDate}">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button class="reservation-incident-btn" data-id="${r.id}">
                    <i class="bi bi-exclamation-triangle me-1"></i>Report Incident
                </button>
            </div>` : ''}
        `;

            gridEl.appendChild(card);
        });

        // Adjuntar listeners de cancelación
        document.querySelectorAll('.reservation-cancel-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                cancelId = this.dataset.id;
                document.getElementById('cancel-detail').innerHTML = `
                <p><strong>${this.dataset.car}</strong></p>
                <p>${this.dataset.start} → ${this.dataset.end}</p>
            `;
                const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
                modal.show();
            });
        });



        // Adjuntar detectores de incidentes
        document.querySelectorAll('.reservation-incident-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                openIncidentModal(this.dataset.id);
            });
        });

        // Animar tarjetas
        if (typeof gsap !== 'undefined') {
            gsap.from('.reservation-card', {
                y: 40,
                opacity: 0,
                duration: 0.5,
                stagger: 0.08,
                ease: 'power3.out'
            });
        }
    }

    // Cancelar reserva 
    document.getElementById('confirm-cancel-btn').addEventListener('click', function () {
        if (!cancelId) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat reservations-spin me-1"></i>Cancelling...';

        fetch('/wp-json/booking/v1/cancel-reservation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': neuvoData.nonce // <-- VARIABLE PUENTE DE PHP
            },
            body: JSON.stringify({
                reservation_id: cancelId
            })
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) throw new Error(data.message || 'Server error');
                    return data;
                });
            })
            .then(function (data) {
                if (data.success) {
                    loadReservations();
                } else {
                    alert(data.message || 'Error cancelling reservation');
                }

                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Yes, Cancel It';
                bootstrap.Modal.getInstance(document.getElementById('cancelModal')).hide();
                cancelId = null;
            })
            .catch(function (err) {
                console.error('Cancel error:', err);
                alert(err.message || 'Connection error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Yes, Cancel It';
            });
    });

    // Filtro
    document.querySelectorAll('.reservations-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.reservations-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilter(this.dataset.filter);
        });
    });

    function applyFilter(filter) {
        const filtered = filter === 'all' ? allReservations : allReservations.filter(r => r.status === filter);
        renderReservations(filtered);
    }

    // Helpers
    function formatDate(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function dateDiffDays(start, end) {
        const a = new Date(start);
        const b = new Date(end);
        return Math.max(1, Math.round((b - a) / (1000 * 60 * 60 * 24)));
    }

    function capitalize(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getStatusClass(status) {
        const map = { confirmed: 'status-confirmed', completed: 'status-completed', cancelled: 'status-cancelled' };
        return map[status] || 'status-confirmed';
    }

    function getStatusIcon(status) {
        const map = { confirmed: 'bi-check-circle-fill', completed: 'bi-trophy-fill', cancelled: 'bi-x-circle-fill' };
        return map[status] || 'bi-circle';
    }



    // Funciones modales de incidentes
    let incidentReservationId = null;

    function openIncidentModal(reservationId) {
        incidentReservationId = reservationId;
        document.getElementById('incident-type').value = '';
        document.getElementById('incident-description').value = '';
        document.getElementById('incident-images').value = '';
        document.getElementById('incident-images-preview').innerHTML = '';
        document.getElementById('incident-error').style.display = 'none';
        var submitBtn = document.getElementById('submit-incident-btn');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Submit Incident';
        var modal = new bootstrap.Modal(document.getElementById('incidentModal'));
        modal.show();
    }

    document.getElementById('incident-images').addEventListener('change', function () {
        var preview = document.getElementById('incident-images-preview');
        preview.innerHTML = '';
        var files = Array.from(this.files);
        if (files.length > 5) {
            document.getElementById('incident-error').textContent = 'Maximum 5 images allowed.';
            document.getElementById('incident-error').style.display = 'block';
            this.value = '';
            return;
        }
        document.getElementById('incident-error').style.display = 'none';
        files.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,0.15);';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    function closeIncidentModal() {
        var modalEl = document.getElementById('incidentModal');
        var modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
        incidentReservationId = null;
    }

    document.getElementById('submit-incident-btn').addEventListener('click', function () {
        var errorEl = document.getElementById('incident-error');
        var typeValue = document.getElementById('incident-type').value;
        var descValue = document.getElementById('incident-description').value.trim();
        var btn = this;

        if (!typeValue) {
            errorEl.textContent = 'Please select an incident type.';
            errorEl.style.display = 'block';
            return;
        }

        if (!descValue || descValue.length < 10) {
            errorEl.textContent = 'Description must be at least 10 characters.';
            errorEl.style.display = 'block';
            return;
        }

        errorEl.style.display = 'none';
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat reservations-spin me-1"></i>Submitting...';

        var imageFiles = document.getElementById('incident-images').files;
        var fetchOptions = {};

        if (imageFiles && imageFiles.length > 0) {
            var formData = new FormData();
            formData.append('reservation_id', parseInt(incidentReservationId));
            formData.append('type', typeValue);
            formData.append('description', descValue);
            for (var i = 0; i < imageFiles.length; i++) {
                formData.append('images[]', imageFiles[i]);
            }
            fetchOptions = {
                method: 'POST',
                headers: { 'X-WP-Nonce': neuvoData.nonce },
                body: formData
            };
        } else {
            fetchOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': neuvoData.nonce
                },
                body: JSON.stringify({
                    reservation_id: parseInt(incidentReservationId),
                    type: typeValue,
                    description: descValue
                })
            };
        }

        fetch('/wp-json/booking/v1/report-incident', fetchOptions)
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) throw new Error(data.message || 'Server error');
                    return data;
                });
            })
            .then(function (data) {
                if (data.success) {
                    closeIncidentModal();
                    var successMsg = document.getElementById('incident-success-msg');
                    successMsg.style.display = 'block';
                    setTimeout(function () {
                        successMsg.style.display = 'none';
                    }, 4000);
                    loadReservations();
                } else {
                    errorEl.textContent = data.message || 'Error reporting incident.';
                    errorEl.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Submit Incident';
                }
            })
            .catch(function (err) {
                console.error('Incident error:', err);
                errorEl.textContent = err.message || 'Connection error. Please try again.';
                errorEl.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Submit Incident';
            });
    });

    document.getElementById('incidentModal').addEventListener('hidden.bs.modal', function () {
        incidentReservationId = null;
        document.getElementById('incident-type').value = '';
        document.getElementById('incident-description').value = '';
        document.getElementById('incident-images').value = '';
        document.getElementById('incident-images-preview').innerHTML = '';
        document.getElementById('incident-error').style.display = 'none';
    });

    loadReservations();
});