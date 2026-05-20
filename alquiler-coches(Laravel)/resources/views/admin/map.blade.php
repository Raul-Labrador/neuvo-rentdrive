<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEUVO - Live Map</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/map.css') }}">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

<div class="layout">
  <aside class="panel">
    <a href="{{ route('admin.cars.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Vehicles
    </a>
    <div class="sidebar-card">
      <div class="sidebar-eyebrow"><i class="bi bi-car-front"></i> Monitored vehicle</div>
      <div class="sidebar-title">{{ trim($car->name . ' ' . $car->model) ?: 'Unknown Vehicle' }}</div>
      
      <div style="font-size: 0.85rem; color: var(--text-2); margin-top: 5px; font-family: monospace; letter-spacing: 1px;">
        <i class="bi bi-upc-scan"></i> {{ $car->plate ?: 'No plate' }}
      </div>

      <div style="color: var(--green); font-size: 0.8rem; font-weight:bold; margin-top:15px;">
        <span style="animation: blink 1s infinite;">●</span> Tracking Live
      </div>
    </div>

    <div class="sidebar-card">
      <div class="sidebar-eyebrow"><i class="bi bi-geo-alt"></i> GPS Location</div>
      <div class="gps-info-grid">
        <div class="gps-box">
          <div class="gps-label">Latitude</div>
          <div id="cLat" class="gps-val">Searching...</div>
        </div>
        <div class="gps-box">
          <div class="gps-label">Longitude</div>
          <div id="cLng" class="gps-val">Searching...</div>
        </div>
      </div>
    </div>
  </aside>

  <main class="map-container">
    <div class="map-window">
      <div class="map-window-bar">
        <div class="map-window-title"><i class="bi bi-radar"></i> Live Radar Feed</div>
      </div>
      <div id="map"></div>
      <div class="info-pill">
        <div style="font-size: 0.55rem; letter-spacing: 2px; color: var(--text-3); text-transform: uppercase;">Last GPS ping</div>
        <div id="ipUpd" style="font-family: monospace; font-size: 0.9rem; margin-top:4px;">--:--:--</div>
      </div>
    </div>
  </main>
</div>

<script>
    window.NeuvoApp = {
        carId: {{ $car->id }}
    };
</script>

<script src="{{ asset('assets/js/admin/map.js') }}"></script>

</body>
</html>