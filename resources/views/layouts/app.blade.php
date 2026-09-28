<!doctype html>
<html lang="en">
  <head>
    @include('partials.head')
  </head>

  <body>
    @yield('navbar')

    @yield('content')

    @yield('footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
  </body>
</html>
