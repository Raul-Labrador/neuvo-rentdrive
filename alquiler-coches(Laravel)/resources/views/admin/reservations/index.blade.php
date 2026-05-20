<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Reservations</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/reservations/index.css') }}">
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
    <a href="{{ route('admin.reservations.index') }}" class="nav-item active">
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
    <div class="topbar-title">Reservations</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="flash-msg flash-error"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
  @endif

  <!-- RESERVATIONS SECTION -->
  <section class="dash-section active" id="section-reservations">
    <div class="section-header">
      <div>
        <h1 class="section-title">Reservations</h1>
        <p class="section-subtitle">Manage all reservations. Complete return inspections for confirmed bookings.</p>
      </div>
    </div>

    <!-- Toolbar with Search and Filters -->
    <div class="table-toolbar">
      <input type="text" class="search-input" id="resSearch" placeholder="Search by customer, vehicle, ID...">
      
      <div class="filter-pills" style="margin-top:0;">
        <a href="{{ route('admin.reservations.index') }}" class="filter-pill {{ $currentFilter === '' ? 'active' : '' }}">
          <i class="bi bi-grid me-1"></i> All <span class="res-count">({{ $reservations->count() }})</span>
        </a>
        <a href="{{ route('admin.reservations.index', ['status' => 'confirmed']) }}" class="filter-pill {{ $currentFilter === 'confirmed' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#2563eb;"></i> Confirmed
        </a>
        <a href="{{ route('admin.reservations.index', ['status' => 'active']) }}" class="filter-pill {{ $currentFilter === 'active' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#d97706;"></i> Active
        </a>
        <a href="{{ route('admin.reservations.index', ['status' => 'completed']) }}" class="filter-pill {{ $currentFilter === 'completed' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#2ecc71;"></i> Completed
        </a>
        <a href="{{ route('admin.reservations.index', ['status' => 'cancelled']) }}" class="filter-pill {{ $currentFilter === 'cancelled' ? 'active' : '' }}">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem; color:#e74c3c;"></i> Cancelled
        </a>
      </div>
    </div>

    <div class="table-card">
      <table class="dash-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Dates</th>
            <th>Total</th>
            <th>Status</th>
            <th>Return</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="resTableBody">
          @forelse($reservations as $reservation)
            <tr data-search="{{ strtolower(($reservation->customer_name ?? '') . ' ' . ($reservation->customer_email ?? '') . ' ' . ($reservation->car ? ($reservation->car->brand . ' ' . $reservation->car->model) : '') . ' ' . $reservation->id) }}">
              <td>{{ $reservation->id }}</td>
              <td>
                <div style="font-size:0.82rem; font-weight:500; color:var(--clr-white);">{{ $reservation->customer_name ?? '—' }}</div>
                <div style="font-size:0.68rem; color:var(--clr-muted2);">{{ $reservation->customer_email ?? '' }}</div>
              </td>
              <td>
                @if($reservation->car)
                  <strong>{{ $reservation->car->brand }}</strong> {{ $reservation->car->model }}
                @else
                  <span style="color:var(--clr-muted);">—</span>
                @endif
              </td>
              <td style="font-size:0.78rem; color:var(--clr-muted);">
                {{ $reservation->start_date ? $reservation->start_date->format('d M') : '—' }}
                → {{ $reservation->end_date ? $reservation->end_date->format('d M Y') : '—' }}
              </td>
              <td style="font-size:0.82rem; font-weight:600;">€{{ number_format($reservation->total_price ?? 0, 2) }}</td>
              <td><span class="badge-status badge-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span></td>
              <td>
                @if($reservation->vehicleReturn)
                  <a href="{{ route('admin.returns.show', $reservation->id) }}" class="returned-badge" style="text-decoration:none;">
                    <i class="bi bi-check-circle-fill"></i> Returned
                  </a>
                @elseif($reservation->status === 'confirmed' || $reservation->status === 'active')
                  <a href="{{ route('admin.returns.create', ['reservation_id' => $reservation->id]) }}" class="btn-return">
                    <i class="bi bi-clipboard-check"></i> Inspect Return
                  </a>
                @else
                  <span style="font-size:0.72rem; color:var(--clr-muted2);">—</span>
                @endif
              </td>
              <td>
                @if($reservation->vehicleReturn)
                  <a href="{{ route('admin.returns.show', $reservation->id) }}" class="view-link">
                    <i class="bi bi-eye me-1"></i>View
                  </a>
                @else
                  <span style="font-size:0.72rem; color:var(--clr-muted2);">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="empty-row">No reservations found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });

  // Live Search Filter
  const resSearch = document.getElementById('resSearch');
  const resTableBody = document.getElementById('resTableBody');
  
  if (resSearch) {
    resSearch.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      const rows = resTableBody.querySelectorAll('tr[data-search]');
      
      rows.forEach(row => {
        const text = row.getAttribute('data-search');
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }

  setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
      el.style.opacity='0'; el.style.transition='opacity 0.4s';
      setTimeout(()=>el.remove(), 400);
    });
  }, 5000);
</script>
</body>
</html>
