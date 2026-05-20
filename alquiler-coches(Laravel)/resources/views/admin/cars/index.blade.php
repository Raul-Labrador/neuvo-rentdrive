<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Vehicles</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
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
    <a href="{{ route('admin.cars.index') }}" class="nav-item active">
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
    <div class="topbar-title">Vehicles</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif

  <!-- VEHICLES SECTION -->
  <section class="dash-section active" id="section-vehicles">
    <div class="section-header">
      <div>
        <h1 class="section-title">Vehicles</h1>
        <p class="section-subtitle">Manage your vehicle fleet. Changes are automatically synchronized with WordPress.</p>
      </div>
      <a href="{{ route('admin.cars.create') }}" class="btn-neuvo">
        <i class="bi bi-plus-lg"></i> Add Vehicle
      </a>
    </div>

    <div class="table-toolbar">
      <input type="text" class="search-input" id="vehicleSearch" placeholder="Search by brand, model, name...">
      <select class="filter-select" id="vehicleFilterFuel">
        <option value="">All Fuels</option>
        <option>Gasoline</option><option>Diesel</option><option>Electric</option>
        <option>Hybrid</option><option>PHEV</option><option>MHEV</option>
        <option>LPG</option><option>CNG</option>
      </select>
      <select class="filter-select" id="vehicleFilterStatus">
        <option value="">All Status</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <div class="table-card">
      <table class="dash-table">
        <thead>
          <tr>
            <th>Plate</th>
            <th>Brand / Model</th>
            <th>Year</th>
            <th>Fuel</th>
            <th>Transmission</th>
            <th>Price/Day</th>
            <th>KM</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="vehiclesTableBody">
          @forelse($cars as $car)
            <tr
              data-search="{{ strtolower($car->brand . ' ' . $car->model . ' ' . $car->name) }}"
              data-fuel="{{ $car->fuel }}"
              data-status="{{ $car->is_active ? '1' : '0' }}"
            >
              <td>
                <strong>{{ $car->plate ?? '—' }}</strong>
              </td>
              <td>
                <strong>{{ $car->brand }}</strong> {{ $car->model }}
                <div style="font-size:0.72rem; color:var(--clr-muted);">{{ $car->name }}</div>
              </td>
              <td>{{ $car->year ?? '—' }}</td>
              <td>{{ $car->fuel ?? '—' }}</td>
              <td>{{ $car->transmission ?? '—' }}</td>
              <td>€{{ $car->price_per_day }}</td>
              <td>{{ $car->km ? number_format($car->km).' km' : '—' }}</td>
              <td>
                @if($car->is_active)
                  <span class="badge-status badge-active">Active</span>
                @else
                  <span class="badge-status badge-inactive">Inactive</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.cars.map', $car->id) }}" class="action-btn" title="Live Map" style="color: #1a73e8;">
                  <i class="bi bi-geo-alt"></i>
                </a>
                <a href="{{ route('admin.cars.edit', $car) }}" class="action-btn" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <button class="action-btn del" title="Delete"
                  onclick="openDeleteModal('{{ route('admin.cars.destroy', $car) }}', '{{ $car->brand }} {{ $car->model }}')">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="empty-row">No vehicles registered.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box modal-sm">
    <div class="modal-hdr">
      <h2 class="modal-title">Confirm Delete</h2>
      <button class="modal-close" onclick="closeModal('deleteModal')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body-text">
      <i class="bi bi-exclamation-triangle-fill delete-warn-icon"></i>
      <p id="deleteModalMsg">Are you sure you want to delete this vehicle? This will also remove it from WordPress.</p>
    </div>
    <div class="modal-actions">
      <button class="btn-neuvo-outline" onclick="closeModal('deleteModal')">Cancel</button>
      <form id="deleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger"><i class="bi bi-trash"></i> Delete</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin/cars/index.js') }}"></script>
</body>
</html>