<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <a class="navbar-brand brand-logo font-weight-bold text-primary" href="/" style="font-size: 24px; text-decoration: none;">
      <i class="mdi mdi-cube-outline"></i> Project
    </a>
    <a class="navbar-brand brand-logo-mini font-weight-bold text-primary" href="/" style="font-size: 26px; text-decoration: none;">
      <i class="mdi mdi-cube-outline"></i>
    </a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-stretch">
    <!-- Tombol Hamburger -->
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-menu"></span>
    </button>
    
    <ul class="navbar-nav navbar-nav-right">
      <!-- Profile section tanpa dropdown dan gambar API inisial nama -->
      <li class="nav-item nav-profile">
        <a class="nav-link" href="#">
          <div class="nav-profile-img">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="image">
            <span class="availability-status online"></span>
          </div>
          <div class="nav-profile-text">
            <p class="mb-1 text-black">{{ Auth::user()->name }}</p>
          </div>
        </a>
      </li>

      <!-- Fitur Notifikasi Dipertahankan -->
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
          <i class="mdi mdi-bell-outline"></i>
          <span class="count-symbol bg-danger"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
          <h6 class="p-3 mb-0">Notifications</h6>
          <div class="dropdown-divider"></div>
          <!-- Masukkan daftar item Notifikasi disini... -->
          <h6 class="p-3 mb-0 text-center">See all notifications</h6>
        </div>
      </li>

      <!-- Diganti jadi icon logout (mdi-logout) -->
      <li class="nav-item nav-logout d-none d-lg-block">
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="mdi mdi-logout"></i>
        </a>
      </li>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
      
    </ul>
    
    <!-- Tombol Toggle Garis 3 Versi Mobile -->
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>