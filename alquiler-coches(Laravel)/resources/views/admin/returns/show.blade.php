<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Inspection Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/returns/show.css') }}">
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
    <div class="topbar-title">Inspection Details — Reservation #{{ $vehicleReturn->reservation_id }}</div>
  </header>

  <!-- DETAIL VIEW -->
  <section class="dash-section active">

    <div class="section-header">
      <div>
        <h1 class="section-title">Return Inspection</h1>
        <p class="section-subtitle">Read-only view of the completed inspection for reservation #{{ $vehicleReturn->reservation_id }}.</p>
      </div>
    </div>

    <div class="detail-grid">

      <!-- LEFT COLUMN -->
      <div>

        <!-- Reservation Info -->
        <div class="detail-card">
          <div class="detail-hdr"><i class="bi bi-calendar-check-fill"></i> Reservation</div>
          <div class="detail-body">
            <div class="detail-row">
              <span class="detail-label">Reservation ID</span>
              <span class="detail-value">#{{ $vehicleReturn->reservation->id }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Customer</span>
              <span class="detail-value">{{ $vehicleReturn->reservation->customer_name ?? '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Email</span>
              <span class="detail-value" style="font-size:0.78rem;">{{ $vehicleReturn->reservation->customer_email ?? '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Pick-up</span>
              <span class="detail-value">{{ $vehicleReturn->reservation->start_date ? $vehicleReturn->reservation->start_date->format('d M Y') : '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Return Date</span>
              <span class="detail-value">{{ $vehicleReturn->reservation->end_date ? $vehicleReturn->reservation->end_date->format('d M Y') : '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Status</span>
              <span class="badge-status badge-{{ $vehicleReturn->reservation->status }}">{{ ucfirst($vehicleReturn->reservation->status) }}</span>
            </div>
          </div>
        </div>

        <!-- Vehicle Info -->
        @if($vehicleReturn->reservation->car)
        <div class="detail-card">
          <div class="detail-hdr"><i class="bi bi-car-front-fill"></i> Vehicle</div>
          <div class="detail-body">
            <div class="detail-row">
              <span class="detail-label">Vehicle</span>
              <span class="detail-value">{{ $vehicleReturn->reservation->car->brand }} {{ $vehicleReturn->reservation->car->model }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Name</span>
              <span class="detail-value">{{ $vehicleReturn->reservation->car->name }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Current Status</span>
              <span class="badge-status badge-{{ $vehicleReturn->reservation->car->status ?? 'available' }}">{{ ucfirst($vehicleReturn->reservation->car->status ?? 'available') }}</span>
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- RIGHT COLUMN -->
      <div>

        <!-- Inspection Data -->
        <div class="detail-card">
          <div class="detail-hdr"><i class="bi bi-clipboard-check"></i> Inspection Data</div>
          <div class="detail-body">
            <div class="detail-row">
              <span class="detail-label">KM at Return</span>
              <span class="detail-value" style="font-weight:700;">{{ number_format($vehicleReturn->km_returned) }} km</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Vehicle Clean</span>
              <span class="detail-value">
                @if($vehicleReturn->is_clean)
                  <span class="flag-yes"><i class="bi bi-check-circle-fill"></i> Yes</span>
                @else
                  <span class="flag-warn"><i class="bi bi-x-circle-fill"></i> No</span>
                @endif
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Needs Review</span>
              <span class="detail-value">
                @if($vehicleReturn->needs_review)
                  <span class="flag-warn"><i class="bi bi-search"></i> Yes</span>
                @else
                  <span class="flag-no">No</span>
                @endif
              </span>
            </div>

            <div class="detail-row">
              <span class="detail-label">Final Car Status</span>
              <span class="badge-status badge-{{ $vehicleReturn->final_car_status ?? 'available' }}">{{ ucfirst($vehicleReturn->final_car_status ?? '—') }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Returned At</span>
              <span class="detail-value">{{ $vehicleReturn->returned_at ? $vehicleReturn->returned_at->format('d M Y — H:i') : '—' }}</span>
            </div>
          </div>
        </div>

        <!-- Damages -->
        <div class="detail-card">
          <div class="detail-hdr"><i class="bi bi-exclamation-triangle-fill"></i> Damages</div>
          <div class="detail-body">
            @if($vehicleReturn->damages)
              <div class="text-block">{{ $vehicleReturn->damages }}</div>
            @else
              <div class="text-block empty">No damages reported.</div>
            @endif
          </div>
        </div>

        <!-- Notes -->
        <div class="detail-card">
          <div class="detail-hdr"><i class="bi bi-chat-left-text-fill"></i> Notes</div>
          <div class="detail-body">
            @if($vehicleReturn->notes)
              <div class="text-block">{{ $vehicleReturn->notes }}</div>
            @else
              <div class="text-block empty">No notes.</div>
            @endif
          </div>
        </div>

      </div>

    </div>

    <!-- Actions -->
    <div class="actions-bar">
      <a href="{{ route('admin.reservations.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to Reservations
      </a>
    </div>

  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });
</script>
</body>
</html>
