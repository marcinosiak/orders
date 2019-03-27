<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Clear Template Project</title>
    <base href="{{URL::asset('/')}}" target="_blank">
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('css/app.css')}}">
    <link rel="stylesheet" href="{{url('css/sticky-footer-navbar.css')}}">
    <link rel="stylesheet" href="{{url('css/bootstrap-datetimepicker.css')}}">

    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

    <link rel="stylesheet" href="{{url('css/style.css')}}">

    <script src="{{url('js/jquery-3.3.1.min.js')}}" type="text/javascript"></script>

  </head>
  <body>

    {{-- @include('partials.nav') --}}

    <div class="container-fluid">
      @yield('content')
    </div>

    @include('partials.footer')

    <script src="{{url('js/popper.min.js')}}" type="text/javascript"></script>
    <script src="{{url('js/bootstrap.min.js')}}" type="text/javascript"></script>
    <script src="{{url('js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('js/bootstrap-datetimepicker.pl.js')}}" type="text/javascript"></script>
    <script src="{{url('js/moment.js')}}" type="text/javascript"></script>
  </body>
</html>
