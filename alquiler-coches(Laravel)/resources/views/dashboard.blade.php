<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Admin Dashboard</title>
  <meta name="description" content="NEUVO Admin Dashboard - Vehicle, customer and reservation management.">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
</head>
<body>

<div id="toastContainer" class="toast-container"></div>

<!--  SIDEBAR  -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="brand-logo">NEUVO</span>
    <span class="brand-sub">Admin</span>
  </div>
  <nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}" class="nav-item active">
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
    <div class="topbar-title" id="topbarTitle">Overview</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="flash-msg flash-error"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
  @endif

  <!-- OVERVIEW SECTION -->
  <section class="dash-section active" id="section-overview">
    <div class="section-header">
      <div>
        <h1 class="section-title">Dashboard Overview</h1>
        <p class="section-subtitle">Welcome back, {{ Auth::user()->name }}. Here is today's summary.</p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ $totalCars ?? 0 }}</span>
          <span class="stat-label">Vehicles</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ $totalClients ?? 0 }}</span>
          <span class="stat-label">Clients</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ $totalReservations ?? 0 }}</span>
          <span class="stat-label">Reservations</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(46, 204, 113, 0.12); color: var(--clr-green);"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ $resolvedIncidents ?? 0 }}</span>
          <span class="stat-label">Resolved Incidents</span>
        </div>
      </div>

    </div>

    <div class="overview-grid">
      <!-- Latest vehicles -->
      <div class="dash-card">
        <div class="dash-card-header" style="display:flex;align-items:center;justify-content:space-between;">
          <h3>Latest Vehicles</h3>
          <a href="{{ route('admin.cars.index') }}" class="btn-neuvo-outline btn-sm">View all</a>
        </div>
        <div class="dash-card-body" style="padding:0;">
          @if(isset($recentCars) && count($recentCars))
            @foreach($recentCars as $car)
              <div class="ov-item" style="padding:12px 20px;">
                <div>
                  <div class="ov-label">{{ $car->brand }} {{ $car->model }}</div>
                  <div class="ov-sub">{{ $car->year }} · {{ $car->fuel ?? '—' }} · {{ $car->price_per_day }}€/day</div>
                </div>
                @if($car->is_active)
                  <span class="badge-status badge-active">Active</span>
                @else
                  <span class="badge-status badge-inactive">Inactive</span>
                @endif
              </div>
            @endforeach
          @else
            <p class="empty-msg">No vehicles registered yet.</p>
          @endif
        </div>
      </div>

      <!-- Quick actions -->
      <div class="dash-card">
        <div class="dash-card-header"><h3>Quick Actions</h3></div>
        <div class="dash-card-body">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('admin.cars.create') }}" class="btn-neuvo" style="justify-content:flex-start;">
              <i class="bi bi-plus-lg"></i> Add Vehicle
            </a>
            <a href="{{ route('admin.cars.index') }}" class="btn-neuvo-outline" style="justify-content:flex-start;">
              <i class="bi bi-car-front-fill"></i> Manage Vehicles
            </a>
          </div>
          <div style="margin-top:22px;padding-top:18px;border-top:1px solid var(--clr-border);">
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--clr-muted);margin-bottom:10px;">WP Synchronization</div>
            <div style="display:flex;gap:8px;align-items:center;">
              <div class="pulse-dot"></div>
              <span style="font-size:0.78rem;color:var(--clr-muted);">WordPress integration active</span>
            </div>
            <p style="font-size:0.72rem;color:var(--clr-muted2);margin-top:8px;line-height:1.6;">Vehicles are automatically synchronized with WordPress upon creation or editing.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>
</html>
