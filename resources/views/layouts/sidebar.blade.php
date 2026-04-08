<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('template/assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">David Grey. H</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
        <!-- Mulai Menu Vendor -->
    <li class="nav-item pt-3">
      <span class="nav-link text-muted" style="font-size: 12px; font-weight: bold; text-transform: uppercase;">
        AREA VENDOR
      </span>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('vendor.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#vendor-menu" aria-expanded="{{ request()->routeIs('vendor.*') ? 'true' : 'false' }}" aria-controls="vendor-menu">
        <span class="menu-title">Kelola Toko</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-store menu-icon"></i> <!-- Icon Toko -->
      </a>
      <div class="collapse {{ request()->routeIs('vendor.*') ? 'show' : '' }}" id="vendor-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" href="{{ route('vendor.dashboard') }}">Dashboard Vendor</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.menu.*') ? 'active' : '' }}" href="{{ route('vendor.menu.index') }}">Master Menu</a>
          </li>
        </ul>
      </div>
    </li>
    <!-- Akhir Menu Vendor -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('buku*') ? 'active' : '' }}" href="/buku">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}" href="/kategori">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-tag menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('pdf*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#pdf-menu" aria-expanded="{{ request()->is('pdf*') ? 'true' : 'false' }}" aria-controls="pdf-menu">
        <span class="menu-title">Generate PDF</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-file-pdf menu-icon"></i>
      </a>
      <div class="collapse {{ request()->is('pdf*') ? 'show' : '' }}" id="pdf-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->is('pdf/sertifikat*') ? 'active' : '' }}" href="/pdf/sertifikat">Sertifikat</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->is('pdf/undangan*') ? 'active' : '' }}" href="/pdf/undangan">Undangan</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('barang*') ? 'active' : '' }}" href="/barang/index">
        <span class="menu-title">Barang</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('barang-sementara*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#barang-trial-menu" aria-expanded="{{ request()->is('barang-sementara*') ? 'true' : 'false' }}" aria-controls="barang-trial-menu">
        <span class="menu-title">Barang Trial</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-flask menu-icon"></i>
      </a>
      <div class="collapse {{ request()->is('barang-sementara*') ? 'show' : '' }}" id="barang-trial-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.sementara') ? 'active' : '' }}" href="{{ route('barang.sementara') }}">Barang Sementara</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.sementara.dt') ? 'active' : '' }}" href="{{ route('barang.sementara.dt') }}">Barang Sementara (DT)</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('pilih.kota') }}">
        <span class="menu-title">Pilih Kota</span>
        <i class="mdi mdi-map-marker menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('studi-kasus/wilayah*') ? 'active' : '' }}" href="{{ route('wilayah.index') }}">
        <span class="menu-title"> Wilayah </span>
        <i class="mdi mdi-map-search menu-icon"></i>
      </a>
    </li>
    {{-- Menu week4 disembunyikan karena hanya untuk latihan --}}
    {{--
    <li class="nav-item">
      <a class="nav-link" href="{{ route('week4.index') }}">
        <span class="menu-title">latihan ajax</span>
        <i class="mdi mdi-ajax menu-icon"></i>
      </a>
    </li>
    --}}
    <li class="nav-item">
    <a class="nav-link" href="{{ route('pos.index') }}">
        <span class="menu-title">POS / Kasir</span>
        <i class="mdi mdi-cart menu-icon"></i>
    </a>
    </li>
  </ul>
</nav>
