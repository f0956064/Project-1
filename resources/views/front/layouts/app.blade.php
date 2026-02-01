<!doctype html>
<html lang="en">
  @include('front.components.stylesheets')
  <body class="{{ isset($frontAuth) && $frontAuth ? 'front-auth' : 'front-app' }}">
  @include('front.components.head')
    <main class="container" style="padding-top: 12px;">
      @yield('content')
    </main>
    @include('front.components.scripts')
  </body>
</html>