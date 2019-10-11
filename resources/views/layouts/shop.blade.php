<!DOCTYPE html>
<html lang="en">

  @include('layouts.includes.header')

  <body class="goto-here">

    @yield('content')

    @include('layouts.includes.footer')

    @include('layouts.includes.script')

  </body>
</html>
