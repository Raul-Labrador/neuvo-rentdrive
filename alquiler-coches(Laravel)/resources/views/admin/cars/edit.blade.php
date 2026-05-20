<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NEUVO — Edit: {{ $car->brand }} {{ $car->model }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
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
    <div class="topbar-title">Edit Vehicle</div>
  </header>

  <div class="section-header">
    <div>
      <h1 class="section-title">Edit Vehicle</h1>
      <p class="section-subtitle">{{ $car->brand }} {{ $car->model }} · Slug: <code style="background:var(--clr-card2);padding:2px 8px;border-radius:4px;font-size:0.78rem;">{{ $car->slug }}</code></p>
    </div>
    <a href="{{ route('admin.cars.index') }}" class="btn-neuvo-outline">
      <i class="bi bi-arrow-left"></i> Back to Vehicles
    </a>
  </div>

  @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="flash-msg flash-error-box">
      <i class="bi bi-exclamation-circle-fill"></i>
      <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.cars.update', $car) }}" enctype="multipart/form-data" id="vehicleForm" novalidate>
    @csrf
    @method('PUT')

    <!-- Core Data -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-card-list"></i>
        <span class="form-card-title">Main Data</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-2">
          <div class="form-group form-group-full">
            <label>Name *</label>
            <input type="text" name="name" class="form-ctrl @error('name') invalid @enderror" value="{{ old('name', $car->name) }}" placeholder="e.g. BMW Serie 3 325d" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group form-group-full">
            <label>Description *</label>
            <textarea name="description" class="form-ctrl @error('description') invalid @enderror">{{ old('description', $car->description) }}</textarea>
            @error('description')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Price / Day (€) *</label>
            <input type="number" step="0.01" name="price_per_day" class="form-ctrl @error('price_per_day') invalid @enderror" value="{{ old('price_per_day', $car->price_per_day) }}" placeholder="99.00" required>
            @error('price_per_day')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Sale Price (€)</label>
            <input type="number" step="0.01" name="price" class="form-ctrl" value="{{ old('price', $car->price) }}" placeholder="0.00">
          </div>
          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $car->is_active) ? 'checked' : '' }}>
              <label for="is_active">Active (visible in WordPress)</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehicle Details -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-car-front-fill"></i>
        <span class="form-card-title">Vehicle Details</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-3">
          <div class="form-group">
            <label>Plate *</label>
            <input type="text" name="plate" class="form-ctrl" value="{{ old('plate', $car->plate) }}" placeholder="7675 NJD">
          </div>
          <div class="form-group">
            <label>Brand *</label>
            <input type="text" name="brand" class="form-ctrl" value="{{ old('brand', $car->brand) }}" placeholder="BMW">
          </div>
          <div class="form-group">
            <label>Model *</label>
            <input type="text" name="model" class="form-ctrl" value="{{ old('model', $car->model) }}" placeholder="Serie 3">
          </div>
          <div class="form-group">
            <label>Year</label>
            <input type="number" name="year" class="form-ctrl" value="{{ old('year', $car->year) }}" placeholder="2024" min="1990" max="2030">
          </div>
          <div class="form-group">
            <label>Color</label>
            <input type="text" name="color" class="form-ctrl" value="{{ old('color', $car->color) }}" placeholder="Midnight Black">
          </div>
          <div class="form-group">
            <label>Kilometers</label>
            <input type="number" name="km" class="form-ctrl" value="{{ old('km', $car->km) }}" placeholder="15000">
          </div>
          <div class="form-group">
            <label>Doors</label>
            <input type="number" name="doors" class="form-ctrl" value="{{ old('doors', $car->doors) }}" placeholder="4" min="2" max="6">
          </div>
          <div class="form-group">
            <label>Seats</label>
            <input type="number" name="seats" class="form-ctrl" value="{{ old('seats', $car->seats) }}" placeholder="5" min="1" max="9">
          </div>
        </div>
      </div>
    </div>

    <!-- Engine -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-gear-fill"></i>
        <span class="form-card-title">Engine & Mechanics</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-3">
          <div class="form-group">
            <label>Fuel</label>
            <select name="fuel" class="form-ctrl">
              <option value="">Select fuel</option>
              @foreach(['Gasoline'=>'Gasoline','Diesel'=>'Diesel','Electric'=>'Electric','Hybrid'=>'Hybrid','PHEV'=>'PHEV','MHEV'=>'Mild Hybrid','LPG'=>'LPG','CNG'=>'CNG','Hydrogen'=>'Hydrogen'] as $v=>$l)
                <option value="{{ $v }}" {{ old('fuel', $car->fuel)==$v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Transmission</label>
            <select name="transmission" class="form-ctrl">
              <option value="">Select</option>
              <option value="Automatic" {{ old('transmission', $car->transmission)=='Automatic' ? 'selected' : '' }}>Automatic</option>
              <option value="Manual"    {{ old('transmission', $car->transmission)=='Manual'    ? 'selected' : '' }}>Manual</option>
              <option value="Semi-Auto" {{ old('transmission', $car->transmission)=='Semi-Auto' ? 'selected' : '' }}>Semi-Auto</option>
            </select>
          </div>
          <div class="form-group">
            <label>Engine Displacement</label>
            <input type="text" name="engine_displacement" class="form-ctrl" value="{{ old('engine_displacement', $car->engine_displacement) }}" placeholder="1998cc">
          </div>
          <div class="form-group">
            <label>Horsepower (HP)</label>
            <input type="number" name="horsepower" class="form-ctrl" value="{{ old('horsepower', $car->horsepower) }}" placeholder="150">
          </div>
          <div class="form-group">
            <label>Emissions Label</label>
            <select name="emissions" class="form-ctrl">
              <option value="">Select</option>
              @foreach(['CERO','ECO','C','B'] as $e)
                <option value="{{ $e }}" {{ old('emissions', $car->emissions)==$e ? 'selected' : '' }}>{{ $e }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-tools"></i>
        <span class="form-card-title">Body</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-2">
          <div class="form-group">
            <label>Body Type</label>
            <select name="body" class="form-ctrl">
              <option value="">Select</option>
              @foreach(['berlina'=>'Sedan / Berlina','familiar'=>'Station Wagon','coupe'=>'Coupé','suv'=>'SUV','minivan'=>'Minivan','cabrio'=>'Convertible','pick-up'=>'Pick-Up','hatchback'=>'Hatchback'] as $v=>$l)
                <option value="{{ $v }}" {{ old('body', $car->body)==$v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Trunk Size</label>
            <select name="trunk" class="form-ctrl">
              <option value="">Select</option>
              <option value="small"  {{ old('trunk', $car->trunk)=='small'  ? 'selected' : '' }}>Small (&lt; 200L)</option>
              <option value="medium" {{ old('trunk', $car->trunk)=='medium' ? 'selected' : '' }}>Medium (200–400L)</option>
              <option value="big"    {{ old('trunk', $car->trunk)=='big'    ? 'selected' : '' }}>Large (&gt; 400L)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Key Features -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-star-fill"></i>
        <span class="form-card-title">Key Features (WordPress)</span>
      </div>
      <div class="form-card-body">
        <div class="tag-input-wrapper">
          <div class="tag-list" id="serviceTagList"></div>
          <div class="tag-add-row">
            <input type="text" id="serviceTagInput" class="form-ctrl" placeholder="Add a feature and press Enter...">
            <button type="button" class="btn-tag-add" id="btnAddService"><i class="bi bi-plus-lg"></i></button>
          </div>
        </div>
        <div id="featuresHiddenInputs"></div>
        <script>
          window.__carFeatures = @json($car->features ?? []);
          window.__oldFeatures = @json(old('features', null));
        </script>
      </div>
    </div>

    <!-- New Images -->
    <div class="form-card">
      <div class="form-card-header">
        <i class="bi bi-cloud-upload"></i>
        <span class="form-card-title">Add New Images (WordPress)</span>
      </div>
      <div class="form-card-body">
        <div class="form-grid form-grid-2">
          <div class="form-group">
            <label>New Featured Image (optional)</label>
            <div class="img-upload-box" id="featuredImageBox">
              <input type="file" name="featured_image" id="vFeaturedImage" accept="image/*" class="img-file-input">
              <div class="img-upload-placeholder" id="featuredPlaceholder">
                <i class="bi bi-image"></i><span>Click to replace featured image</span>
              </div>
              <img id="featuredPreview" class="img-preview hidden" alt="Featured Preview">
              <button type="button" class="img-clear-btn hidden" id="featuredClear"><i class="bi bi-x"></i></button>
            </div>
            @error('featured_image')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Add Gallery Images</label>
            <div class="img-upload-box">
              <input type="file" name="gallery_images[]" id="vGalleryImages" accept="image/*" multiple class="img-file-input">
              <div class="img-upload-placeholder" id="galleryPlaceholder">
                <i class="bi bi-images"></i><span>Click to add gallery images</span>
              </div>
              <div id="galleryPreviewGrid" class="gallery-preview-grid"></div>
            </div>
            @error('gallery_images')<span class="form-error">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.cars.index') }}" class="btn-neuvo-outline">Cancel</a>
      <button type="submit" class="btn-neuvo"><i class="bi bi-cloud-arrow-up"></i> Save & Sync to WordPress</button>
    </div>
  </form>

  <!-- Existing WP Media -->
  @if(isset($mediaUrls) && count($mediaUrls) > 0)
  <div class="form-card" style="margin-top:20px;">
    <div class="form-card-header">
      <i class="bi bi-images"></i>
      <span class="form-card-title">Current WordPress Images</span>
    </div>
    <div class="form-card-body">
      <p style="font-size:0.78rem; color:var(--clr-muted); margin-bottom:16px;">Images currently stored in WordPress. Click the × to permanently delete one.</p>
      <div class="media-grid">
        @foreach($mediaUrls as $media)
          <div class="media-item">
            <img src="{{ $media['url'] }}" alt="WP Image">
            <form method="POST" action="{{ route('admin.cars.destroyMedia', [$car, $media['id']]) }}"
              onsubmit="return confirm('Permanently delete this image from WordPress?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="media-delete-btn" title="Delete from WordPress">
                <i class="bi bi-x"></i>
              </button>
            </form>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin/cars/edit.js') }}"></script>
</body>
</html>