<!doctype html>
<html lang="en">
  @include('front.components.stylesheets')
  <body class="{{ isset($frontAuth) && $frontAuth ? 'front-auth' : 'front-app' }}">
  @include('front.components.head')
    <main class="container">
      @yield('content')
    </main>
    @include('front.components.scripts')
  </body>
</html>