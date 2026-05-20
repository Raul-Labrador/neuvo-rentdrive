<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Incident #{{ $incident->id }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/incidents/show.css') }}">
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
    <a href="{{ route('admin.incidents.index') }}" class="nav-item active">
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
    <div class="topbar-title">Incident #{{ $incident->id }}</div>
  </header>

  <a href="{{ route('admin.incidents.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i> Back to Incidents
  </a>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="validation-errors">
      <strong><i class="bi bi-exclamation-circle-fill me-1"></i>Validation errors:</strong>
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- INCIDENT DETAIL -->
  <section class="dash-section active">

    <div class="detail-grid">

      <!-- LEFT: Incident Info -->
      <div>
        <div class="detail-card">
          <div class="detail-card-header">
            <i class="bi bi-exclamation-triangle-fill"></i> Incident Information
          </div>
          <div class="detail-card-body">
            <div class="detail-row">
              <span class="detail-label">ID</span>
              <span class="detail-value">#{{ $incident->id }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Type</span>
              <span class="detail-value" style="text-transform:capitalize;">{{ str_replace('_', ' ', $incident->type) }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Status</span>
              <span class="detail-value">
                <span class="badge-status badge-{{ $incident->status }}">{{ str_replace('_', ' ', ucfirst($incident->status)) }}</span>
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Reported</span>
              <span class="detail-value">{{ $incident->created_at->format('d M Y — H:i') }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Updated</span>
              <span class="detail-value">{{ $incident->updated_at->format('d M Y — H:i') }}</span>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="detail-card" style="margin-top:20px;">
          <div class="detail-card-header">
            <i class="bi bi-chat-left-text-fill"></i> Customer Description
          </div>
          <div class="detail-card-body">
            <div class="desc-full">{{ $incident->description }}</div>
          </div>
        </div>

        <!-- Admin Notes -->
        @if($incident->admin_notes)
        <div class="detail-card" style="margin-top:20px;">
          <div class="detail-card-header">
            <i class="bi bi-journal-text"></i> Admin Notes
          </div>
          <div class="detail-card-body">
            <div class="desc-full" style="border-color:rgba(59,130,246,0.3); background:rgba(59,130,246,0.05);">{{ $incident->admin_notes }}</div>
          </div>
        </div>
        @endif

        <!-- Incident Images -->
        @if($incident->images && $incident->images->count() > 0)
        <div class="detail-card" style="margin-top:20px;">
          <div class="detail-card-header">
            <i class="bi bi-camera-fill"></i> Attached Photos ({{ $incident->images->count() }})
          </div>
          <div class="detail-card-body">
            <div class="incident-images-grid">
              @foreach($incident->images as $img)
                <div class="incident-img-item" onclick="openImageOverlay('{{ asset('storage/' . $img->path) }}')">
                  <img src="{{ asset('storage/' . $img->path) }}" alt="Incident photo">
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- RIGHT: Reservation & Vehicle + Update Form -->
      <div>
        <!-- Reservation Info -->
        <div class="detail-card">
          <div class="detail-card-header">
            <i class="bi bi-calendar-check-fill"></i> Reservation
          </div>
          <div class="detail-card-body">
            @if($incident->reservation)
              <div class="detail-row">
                <span class="detail-label">Reservation ID</span>
                <span class="detail-value">#{{ $incident->reservation->id }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Customer</span>
                <span class="detail-value">{{ $incident->reservation->customer_name ?? '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $incident->reservation->customer_email ?? '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">WP User ID</span>
                <span class="detail-value">{{ $incident->wp_user_id }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Pick-up</span>
                <span class="detail-value">{{ $incident->reservation->start_date ? $incident->reservation->start_date->format('d M Y') : '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Drop-off</span>
                <span class="detail-value">{{ $incident->reservation->end_date ? $incident->reservation->end_date->format('d M Y') : '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">{{ ucfirst($incident->reservation->status ?? '—') }}</span>
              </div>
            @else
              <p style="font-size:0.82rem; color:var(--clr-muted);">Reservation not found.</p>
            @endif
          </div>
        </div>

        <!-- Vehicle Info -->
        <div class="detail-card" style="margin-top:20px;">
          <div class="detail-card-header">
            <i class="bi bi-car-front-fill"></i> Vehicle
          </div>
          <div class="detail-card-body">
            @if($incident->car)
              <div class="detail-row">
                <span class="detail-label">Vehicle</span>
                <span class="detail-value"><strong>{{ $incident->car->brand }}</strong> {{ $incident->car->model }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Year</span>
                <span class="detail-value">{{ $incident->car->year ?? '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Fuel</span>
                <span class="detail-value">{{ $incident->car->fuel ?? '—' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Plate / ID</span>
                <span class="detail-value">#{{ $incident->car->id }}</span>
              </div>
            @else
              <p style="font-size:0.82rem; color:var(--clr-muted);">Vehicle not found.</p>
            @endif
          </div>
        </div>

        <!-- Update Form -->
        <div class="detail-card" style="margin-top:20px;">
          <div class="detail-card-header">
            <i class="bi bi-pencil-square"></i> Update Incident
          </div>
          <div class="detail-card-body">
            <form method="POST" action="{{ route('admin.incidents.update', $incident->id) }}" class="update-form">
              @csrf
              @method('PATCH')

              <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-select">
                  <option value="open" {{ $incident->status === 'open' ? 'selected' : '' }}>Open</option>
                  <option value="in_review" {{ $incident->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                  <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                  <option value="dismissed" {{ $incident->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
              </div>

              <div class="form-group">
                <label for="admin_notes">Admin Notes</label>
                <textarea name="admin_notes" id="admin_notes" class="form-textarea" placeholder="Internal notes about this incident...">{{ old('admin_notes', $incident->admin_notes) }}</textarea>
              </div>

              <button type="submit" class="btn-update">
                <i class="bi bi-check-lg"></i> Save Changes
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </section>

</div>

<div class="img-overlay" id="imgOverlay" onclick="this.classList.remove('show')">
  <img id="imgOverlaySrc" src="" alt="Full size">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/incidents/show.js') }}"></script>
</body>
</html>
