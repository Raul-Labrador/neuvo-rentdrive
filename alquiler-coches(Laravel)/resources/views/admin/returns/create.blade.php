<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}"> 
  <title>NEUVO — Return Inspection</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/returns/create.css') }}">
</head>
<body>

<div id="toastContainer" class="toast-container"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="brand-logo">NEUVO</span>
    <span class="brand-sub">Admin</span>
  </div>
  <nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}" class="nav-item">
      <i class="bi bi-grid-1x2-fill"></i><span>Overview</span>
    </a>
    <a href="{{ route('admin.cars.index') }}" class="nav-item">
      <i class="bi bi-car-front-fill"></i><span>Vehicles</span>
    </a>
    <a href="{{ route('admin.clients.index') }}" class="nav-item">
      <i class="bi bi-people-fill"></i><span>Clients</span>
    </a>
    <a href="{{ route('admin.reservations.index') }}" class="nav-item">
      <i class="bi bi-calendar-check-fill"></i><span>Reservations</span>
    </a>
    <a href="{{ route('admin.incidents.index') }}" class="nav-item">
      <i class="bi bi-exclamation-triangle-fill"></i><span>Incidents</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="https://neuvo-app.com//wp-admin" target="_blank" class="nav-item">
      <i class="bi bi-wordpress"></i><span>ADMIN AREA</span>
    </a>
    <a href="https://neuvo-app.com/" target="_blank" class="nav-item">
      <i class="bi bi-box-arrow-up-right"></i><span>SEE THE WEBSITE</span>
    </a>
    <div style="padding:12px 14px 4px; border-top:1px solid var(--clr-border); margin-top:8px;">
      <div style="font-size:0.78rem; font-weight:600; color:var(--clr-white); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
      <div style="font-size:0.65rem; color:var(--clr-muted2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:8px;">{{ Auth::user()->email }}</div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; text-align:left; color:var(--clr-muted);">
          <i class="bi bi-box-arrow-right"></i><span>Log out</span>
        </button>
      </form>
    </div>
  </div>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">

  <header class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <div class="topbar-title">Return Inspection — Reservation #{{ $reservation->id }}</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="flash-msg flash-error"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
  @endif

  @if($errors->any())
    <div class="error-box">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- INSPECTION FORM -->
  <section class="dash-section active">

    <div class="inspect-grid">

      <!-- LEFT: Reservation Summary -->
      <div>
        <div class="summary-card">
          <div class="summary-hdr">
            <i class="bi bi-calendar-check-fill"></i> Reservation Details
          </div>
          <div class="summary-body">
            <div class="summary-row">
              <span class="summary-label">Reservation ID</span>
              <span class="summary-value">#{{ $reservation->id }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Customer</span>
              <span class="summary-value">{{ $reservation->customer_name ?? '—' }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Email</span>
              <span class="summary-value" style="font-size:0.78rem;">{{ $reservation->customer_email ?? '—' }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Vehicle</span>
              <span class="summary-value">
                @if($reservation->car)
                  {{ $reservation->car->brand }} {{ $reservation->car->model }}
                @else
                  —
                @endif
              </span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Pick-up</span>
              <span class="summary-value">{{ $reservation->start_date ? $reservation->start_date->format('d M Y') : '—' }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Return Date</span>
              <span class="summary-value">{{ $reservation->end_date ? $reservation->end_date->format('d M Y') : '—' }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Total Price</span>
              <span class="summary-value" style="color:#2ecc71; font-weight:700;">€{{ number_format($reservation->total_price ?? 0, 2) }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Current Status</span>
              <span class="badge-status badge-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
            </div>
          </div>
        </div>

        @if($reservation->car)
        <div class="summary-card" style="margin-top:16px;">
          <div class="summary-hdr">
            <i class="bi bi-car-front-fill"></i> Vehicle Info
          </div>
          <div class="summary-body">
            <div class="summary-row">
              <span class="summary-label">Name</span>
              <span class="summary-value">{{ $reservation->car->name }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Current KM</span>
              <span class="summary-value">{{ $reservation->car->km ? number_format($reservation->car->km).' km' : '—' }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Car Status</span>
              <span class="summary-value">{{ ucfirst($reservation->car->status ?? 'available') }}</span>
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- RIGHT: Inspection Form -->
      <div>
        <form method="POST" action="{{ route('admin.returns.store') }}">
          @csrf
          <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

          <div class="form-card">
            <div class="form-hdr">
              <i class="bi bi-clipboard-check"></i> Inspection Form
            </div>
            <div class="form-body">

              <!-- KM Returned -->
              <div class="field-group">
                <label class="field-label">Kilometers at Return <span class="req">*</span></label>
                <input type="number" name="km_returned" class="field-input"
                       value="{{ old('km_returned', $reservation->car->km ?? '') }}"
                       min="0" required placeholder="e.g. 55230">
              </div>

              <!-- Is Clean -->
              <div class="field-group">
                <label class="field-label">Vehicle Clean? <span class="req">*</span></label>
                <select name="is_clean" class="field-select" required>
                  <option value="1" {{ old('is_clean', '1') == '1' ? 'selected' : '' }}>Yes — Clean</option>
                  <option value="0" {{ old('is_clean') == '0' ? 'selected' : '' }}>No — Needs cleaning</option>
                </select>
              </div>

              <!-- Damages -->
              <div class="field-group">
                <label class="field-label">Damages</label>
                <textarea name="damages" class="field-textarea" placeholder="Describe any visible damages...">{{ old('damages') }}</textarea>
              </div>

              <!-- Notes -->
              <div class="field-group">
                <label class="field-label">Notes</label>
                <textarea name="notes" class="field-textarea" placeholder="Additional observations...">{{ old('notes') }}</textarea>
              </div>

              <!-- Checkboxes -->
              <div class="field-group">
                <label class="field-label">Flags</label>
                <div class="check-group">
                  <label class="check-item">
                    <input type="hidden" name="needs_review" value="0">
                    <input type="checkbox" name="needs_review" value="1" {{ old('needs_review') ? 'checked' : '' }}>
                    <span>Needs Review</span>
                  </label>

                </div>
              </div>

              <!-- Final Car Status -->
              <div class="field-group">
                <label class="field-label">Set Vehicle Status After Return <span class="req">*</span></label>
                <select name="final_car_status" class="field-select" required>
                  <option value="available" {{ old('final_car_status', 'available') == 'available' ? 'selected' : '' }}>
                    Available — Ready for next rental
                  </option>
                  <option value="maintenance" {{ old('final_car_status') == 'maintenance' ? 'selected' : '' }}>
                    Maintenance — Needs servicing
                  </option>
                  <option value="unavailable" {{ old('final_car_status') == 'unavailable' ? 'selected' : '' }}>
                    Unavailable — Out of service
                  </option>
                </select>
              </div>

              <!-- Action Buttons -->
              <div class="actions-bar">
                <a href="{{ route('admin.reservations.index') }}" class="btn-back">
                  <i class="bi bi-arrow-left"></i> Back to Reservations
                </a>
                <button type="submit" class="btn-submit">
                  <i class="bi bi-check-lg"></i> Complete Inspection
                </button>
              </div>

            </div>
          </div>
        </form>
      </div>

    </div>

  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });

  setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
      el.style.opacity='0'; el.style.transition='opacity 0.4s';
      setTimeout(()=>el.remove(), 400);
    });
  }, 5000);
</script>
</body>
</html>
