<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Incidents</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/incidents/index.css') }}">
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
    <div class="topbar-title">Incidents</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif

  <!-- INCIDENTS SECTION -->
  <section class="dash-section active" id="section-incidents">
    <div class="section-header">
      <div>
        <h1 class="section-title">Incidents</h1>
        <p class="section-subtitle">Customer-reported incidents. Review and manage status.</p>
      </div>
    </div>

    <!-- Filter Pills -->
    <div class="table-toolbar">
      <div class="filter-pills">
        <a href="{{ route('admin.incidents.index') }}" class="filter-pill {{ $currentFilter === '' ? 'active' : '' }}">
          <i class="bi bi-grid me-1"></i> All
        </a>
        <a href="{{ route('admin.incidents.index', ['status' => 'open']) }}" class="filter-pill {{ $currentFilter === 'open' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#d97706;"></i> Open
        </a>
        <a href="{{ route('admin.incidents.index', ['status' => 'in_review']) }}" class="filter-pill {{ $currentFilter === 'in_review' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#2563eb;"></i> In Review
        </a>
        <a href="{{ route('admin.incidents.index', ['status' => 'resolved']) }}" class="filter-pill {{ $currentFilter === 'resolved' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#2ecc71;"></i> Resolved
        </a>
        <a href="{{ route('admin.incidents.index', ['status' => 'dismissed']) }}" class="filter-pill {{ $currentFilter === 'dismissed' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#6b7280;"></i> Dismissed
        </a>
      </div>
    </div>

    <div class="table-card">
      <table class="dash-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Reservation</th>
            <th>Vehicle</th>
            <th>User ID</th>
            <th>Type</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($incidents as $incident)
            <tr>
              <td>{{ $incident->id }}</td>
              <td>#{{ $incident->reservation_id }}</td>
              <td>
                @if($incident->car)
                  <strong>{{ $incident->car->brand }}</strong> {{ $incident->car->model }}
                @else
                  <span style="color:var(--clr-muted);">—</span>
                @endif
              </td>
              <td>{{ $incident->wp_user_id }}</td>
              <td><span class="type-label">{{ str_replace('_', ' ', $incident->type) }}</span></td>
              <td><span class="desc-cell" title="{{ $incident->description }}">{{ Str::limit($incident->description, 50) }}</span></td>
              <td><span class="badge-status badge-{{ $incident->status }}">{{ str_replace('_', ' ', ucfirst($incident->status)) }}</span></td>
              <td style="font-size:0.78rem; color:var(--clr-muted);">{{ $incident->created_at->format('d M Y H:i') }}</td>
              <td>
                <a href="{{ route('admin.incidents.show', $incident->id) }}" class="view-link">
                  <i class="bi bi-eye me-1"></i>View
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="empty-row">No incidents found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/incidents/index.js') }}"></script>
</body>
</html>
