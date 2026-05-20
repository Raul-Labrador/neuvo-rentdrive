<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Edit Client</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" >
</head>
<body>

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
    <div class="topbar-title">Edit Client</div>
  </header>

  <div class="section-header">
    <div>
      <h1 class="section-title">Edit Client</h1>
      <p class="section-subtitle">{{ $client['name'] ?? '' }} ({{ $client['email'] ?? '' }})</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn-neuvo-outline">
      <i class="bi bi-arrow-left"></i> Back to Clients
    </a>
  </div>

  @if($errors->any())
    <div class="flash-msg flash-error-box">
      <i class="bi bi-exclamation-circle-fill"></i>
      <div>
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.clients.update', $client['id']) }}">
    @csrf
    @method('PUT')

    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-person-badge"></i>
        <span class="form-card-title">Client Details</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-2">
          <div class="form-group form-group-full">
            <label>Username (Cannot be changed)</label>
            <input type="text" class="form-ctrl" value="{{ $client['slug'] ?? ($client['username'] ?? '') }}" disabled>
          </div>
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-ctrl @error('first_name') invalid @enderror" value="{{ old('first_name', $client['first_name'] ?? '') }}" placeholder="John">
            @error('first_name')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-ctrl @error('last_name') invalid @enderror" value="{{ old('last_name', $client['last_name'] ?? '') }}" placeholder="Doe">
            @error('last_name')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" class="form-ctrl @error('email') invalid @enderror" value="{{ old('email', $client['email'] ?? '') }}" placeholder="john@example.com" required>
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>New Password (Leave blank to keep current)</label>
            <input type="password" name="password" class="form-ctrl @error('password') invalid @enderror" minlength="6">
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.clients.index') }}" class="btn-neuvo-outline">Cancel</a>
      <button type="submit" class="btn-neuvo"><i class="bi bi-person-check-fill"></i> Save Changes</button>
    </div>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });
</script>
</body>
</html>
