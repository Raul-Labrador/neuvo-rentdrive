<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Clients</title>
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
    <a href="{{ route('admin.cars.index') }}" class="nav-item">
      <i class="bi bi-car-front-fill"></i><span>Vehicles</span>
    </a>
    <a href="{{ route('admin.clients.index') }}" class="nav-item active">
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
    <div class="topbar-title">Clients</div>
  </header>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif

  <!-- CLIENTS SECTION -->
  <section class="dash-section active" id="section-clients">
    <div class="section-header">
      <div>
        <h1 class="section-title">Clients</h1>
        <p class="section-subtitle">Manage your clients and subscribers imported from WordPress.</p>
      </div>
      <a href="{{ route('admin.clients.create') }}" class="btn-neuvo">
        <i class="bi bi-plus-lg"></i> Add Client
      </a>
    </div>

    <div class="table-toolbar">
      <input type="text" class="search-input" id="clientSearch" placeholder="Search by name, email or ID...">
    </div>

    <div class="table-card">
      <table class="dash-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Registered</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="clientsTableBody">
          @forelse($clients as $client)
            <tr
              data-search="{{ strtolower(($client['name'] ?? '') . ' ' . ($client['email'] ?? '') . ' ' . ($client['id'] ?? '')) }}"
            >
              <td>#{{ $client['id'] ?? '—' }}</td>
              <td>
                <strong>{{ $client['name'] ?? '—' }}</strong>
                @if(isset($client['first_name']) || isset($client['last_name']))
                  <div style="font-size:0.72rem; color:var(--clr-muted);">
                    {{ $client['first_name'] ?? '' }} {{ $client['last_name'] ?? '' }}
                  </div>
                @endif
              </td>
              <td><a href="mailto:{{ $client['email'] ?? '' }}" style="color:var(--clr-blue);">{{ $client['email'] ?? '—' }}</a></td>
              <td>{{ $client['slug'] ?? ($client['username'] ?? '—') }}</td>
              <td>{{ isset($client['registered_date']) ? \Carbon\Carbon::parse($client['registered_date'])->format('d M, Y') : '—' }}</td>
              <td>
                <a href="{{ route('admin.clients.edit', $client['id']) }}" class="action-btn" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <button class="action-btn del" title="Delete"
                  onclick="openDeleteModal('{{ route('admin.clients.destroy', $client['id']) }}', '{{ $client['name'] ?? '' }}')">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="empty-row">No clients found or cannot connect to WordPress.</td></tr>
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
      <p id="deleteModalMsg">Are you sure you want to delete this client? This will permanently remove them from WordPress.</p>
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
<script src="{{ asset('assets/admin/clients/index.js') }}"></script>
</body>
</html>
