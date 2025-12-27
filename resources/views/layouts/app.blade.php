{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="ar">
<head>
{{-- Main Title (changeable via @section('title')) --}}
<title>@yield('title', 'مؤسسة البدر')</title>
{{-- [Meta] --}}
<meta charset="utf-8" />
<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
/>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta
  name="description"
  content="مؤسسة البدر للتخاطب وتنمية المهارات والتعليم والتدريب"
/>
<meta
  name="keywords"
  content="مؤسسة البدر, التخاطب, تنمية المهارات, التعليم, التدريب"
/>
<meta name="author" content="Safe Tech" />

<meta name="ast" content="{{ request()->cookie('access_token') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Optional: Include a theme (e.g., dark) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">


{{-- [Favicon] --}}
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />

{{-- [Font] Changa from Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Changa:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- [Font] Family --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}" id="main-font-link" />
{{-- [phosphor Icons] https://phosphoricons.com/ --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
{{-- [Tabler Icons] https://tablericons.com --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
{{-- [Feather Icons] https://feathericons.com --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
{{-- [Font Awesome Icons] https://fontawesome.com/icons --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
{{-- [Material Icons] https://fonts.google.com/icons --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
{{-- [Template CSS Files] --}}
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<script src="{{ asset('assets/js/tech-stack.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />


    <!-- [Font] Family -->
<link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}" id="main-font-link" />
<!-- [Phosphor Icons] https://phosphoricons.com/ -->
<link rel="stylesheet" href="{{ asset('fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('fonts/tabler-icons.min.css') }}" />
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('fonts/feather.css') }}" />
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('fonts/fontawesome.css') }}" />
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('fonts/material.css') }}" />
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('css/style.css') }}" id="main-style-link" />
<script src="{{ asset('js/tech-stack.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/style-preset.css') }}" />


<style>
  body, .pc-sidebar, .pc-header, .card, .btn, .dropdown-item, .nav-link, h1, h2, h3, h4, h5, h6, p, span, label, input, textarea, select {
    font-family: 'Changa', sans-serif !important;
  }

  .datepicker
  {
    width: auto !important;
  }

  /* update primary color root */
  :root {
    --primary-color: #151f42 !important;
  }

</style>

{{-- Allow child views to inject extra CSS if needed --}}


<style>
  mark, .mark { all: unset !important; }
  </style>
  



@stack('styles')



</head>

<body
data-pc-preset="preset-1"
data-pc-sidebar-caption="true"
data-pc-layout="vertical"
data-pc-direction="rtl"
data-pc-theme_contrast=""
data-pc-theme="light"
>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] end -->

<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('sections') }}" class="b-brand text-primary">
        <img src="{{ asset('assets/images/logo-white.png') }}" class="logo" width="200" alt="">
      </a>
    </div>
    <div class="navbar-content">
      <div class="card pc-user-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <img alt="user-image" class="user-avtar wid-45 rounded-circle" src="{{ asset('https://ui-avatars.com/api/?name=' . implode('+', explode(' ', Auth::user()->name)) . '&background=6368a7&color=fff') }}">
            </div>
            <div class="flex-grow-1 ms-3 me-2">
              <h6 class="mb-0 ">{{ Auth::user()->name }}</h6>
            </div>
            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
              <svg class="pc-icon">
                <use xlink:href="#custom-sort-outline"></use>
              </svg>
            </a>
          </div>
          <div class="collapse pc-user-links" id="pc_sidebar_userlink">
            <div class="pt-3">
              <a href="{{ route('profile') }}">
                <i class="ti ti-user"></i>
                <span>الملف الشخصي</span>
              </a>
              <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ti ti-lock"></i>
                <span>تسجيل الخروج</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Main Sidebar Menu --}}
      <ul class="pc-navbar">


        @if (get_area_name() == "admin")
            @include('layouts.menus.admin')
        @endif

        @if (get_area_name() == "instructor")
            @include('layouts.menus.instructor')
        @endif

        @if (get_area_name() == "exam_officer")
          @include('layouts.menus.exam_officer')
      @endif

      @if (get_area_name() == "supervisor")
        @include('layouts.menus.supervisor')
      @endif


      @if (get_area_name() == "reception")
        @include('layouts.menus.reception')
      @endif

      @if (get_area_name() == "specialist")
        @include('layouts.menus.specialist')
      @endif

      @if (get_area_name() == "accountant")
        @include('layouts.menus.accountant')
      @endif





      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->

<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper">
    <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
 
  </div>
</header>
<!-- [ Header ] end -->

{{-- Offcanvas for Announcements --}}
<div
  class="offcanvas pc-announcement-offcanvas offcanvas-end"
  tabindex="-1"
  id="announcement"
  aria-labelledby="announcementLabel"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
    <button
      type="button"
      class="btn btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>
  <div class="offcanvas-body">
    <p class="text-span">Today</p>
    <div class="card mb-3">
      <div class="card-body">
        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
          <div class="badge bg-light-success f-12">Big News</div>
          <p class="mb-0 text-muted">2 min ago</p>
          <span class="badge dot bg-warning"></span>
        </div>
        <h5 class="mb-3">Able Pro is Redesigned</h5>
        <p class="text-muted">
          Able Pro is completely renowed with high aesthetics User Interface.
        </p>
        <img
          src="{{ asset('assets/images/layout/img-announcement-1.png') }}"
          alt="img"
          class="img-fluid mb-3"
        />
        <div class="row">
          <div class="col-12">
            <div class="d-grid">
              <a
                class="btn btn-outline-secondary"
                href="https://1.envato.market/zNkqj6"
                target="_blank"
                >Check Now</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- [ Main Content ] start -->
<div class="pc-container">
  <div class="pc-content">
    {{-- Optional: separate breadcrumb section if you want --}}
    {{-- 
        <div class="page-header">
          @yield('breadcrumb')
        </div> 
    --}}

    <!-- You can show a default breadcrumb here or replace it with a yield -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            @include('layouts.messages')
          </div>
          <div class="col-md-12">
            <ul class="breadcrumb">
                @yield('breadcrumb')
            </ul>
          </div>
          
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">
                @yield('title', 'Dashboard')
              </h2>
            </div>
          </div>

        </div>
      </div>
    </div>

    

    @yield('content')

  </div>
</div>
<footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
    <div class="row">
      <div class="col my-1">
        <p class="m-0">
          جميع الحقوق محفوظة. مؤسسة البدر للتخاطب وتنمية المهارات والتعليم والتدريب
        </p>
      </div>
    </div>
  </div>
</footer>

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>


<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-analytics.js') }}"></script>
<!-- [Page Specific JS] end -->

<!-- Required JS -->

<script>
  layout_change('light');
</script>

<script>
  change_box_container('false');
</script>

<script>
  layout_caption_change('true');
</script>

<script>
  layout_rtl_change('false');
</script>

<script>
  preset_change('preset-1');
</script>

<script>
  main_layout_change('vertical');
</script>

<div
  class="offcanvas border-0 pct-offcanvas offcanvas-end"
  tabindex="-1"
  id="offcanvas_pc_layout"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Settings</h5>
    <button
      type="button"
      class="btn btn-icon btn-link-danger ms-auto"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    >
      <i class="ti ti-x"></i>
    </button>
  </div>
  <div class="pct-body customizer-body">
    <div class="offcanvas-body py-0">
      <ul class="list-group list-group-flush">
        {{-- ... (rest of the "Settings" offcanvas exactly as your code) ... --}}
        <li class="list-group-item">
          <div class="d-grid">
            <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

{{-- Example: let’s keep your final script calls, but also allow child views to push additional scripts --}}
<script>
  function changebrand(presetColor) {
    removeClassByPrefix(document.querySelector('body'), 'preset-');
    document.querySelector('body').classList.add(presetColor);
  }
  localStorage.setItem('layout', 'color-header');
</script>

<script src="https://cdn-script.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>


@stack('scripts')



<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form").forEach(function (form) {
      form.addEventListener("submit", function (event) {
        let submitButton = form.querySelector("[type='submit']");

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.innerHTML = " Loading ...";
        }
      });
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    flatpickr(
      'input[type="date"], input.datepicker',
      {
        dateFormat: "Y-m-d",   // what gets submitted
        altInput: true,
        altFormat: "F j, Y",    // what the user sees
        allowInput: true
      }
    );
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

</body>
</html>
