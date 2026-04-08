<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Koleksi Buku</title>

{{-- GLOBAL STYLE --}}
<link rel="stylesheet" href="{{ asset('template/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('template/assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('template/assets/images/favicon.png') }}">

{{-- DATATABLES CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

{{-- PAGE STYLE --}}
@stack('style')

</head>

<body>
<div class="container-scroller">

    {{-- NAVBAR --}}
    @include('layouts.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        <div class="main-panel">
            <div class="content-wrapper">

                {{-- PAGE CONTENT --}}
                @yield('content')

            </div>

            {{-- FOOTER --}}
            @include('layouts.footer')

        </div>
    </div>
</div>

{{-- GLOBAL JS --}}
<script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('template/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('template/assets/js/misc.js') }}"></script>
<script src="{{ asset('template/assets/js/settings.js') }}"></script>

{{-- DATATABLES JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

{{-- SIDEBAR & NAV LOGIC --}}
<script>
(function($) {
    'use strict';

    $(document).on('click', '[data-bs-toggle="collapse"]', function(e) {
        var $this = $(this);
        var target = $this.attr('href');

        if ($(target).length) {
            e.preventDefault();
            e.stopPropagation();

            $('.sidebar .collapse.show').not(target).collapse('hide');
            $(target).collapse('toggle');

            var isExpanded = $this.attr('aria-expanded') === 'true';
            $this.attr('aria-expanded', !isExpanded);
        }
    });

    $('.sidebar .collapse').on('show.bs.collapse', function() {
        $(this).parent().addClass('active');
    });

    $('.sidebar .collapse').on('hide.bs.collapse', function() {
        $(this).parent().removeClass('active');
    });

    $('[data-toggle="minimize"]').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-icon-only');
    });

    $('[data-toggle="offcanvas"]').on('click', function() {
        $('.sidebar-offcanvas').toggleClass('active');
    });

    $('.sidebar .nav-link').each(function() {
        var current = window.location.pathname;
        var $this = $(this);

        if ($this.attr('href') === current) {
            $this.addClass('active');
            $this.parents('.collapse').addClass('show');
            $this.parents('.nav-item').addClass('active');
        }
    });

})(jQuery);
</script>

{{-- PAGE JS --}}
@stack('script')

</body>
</html>