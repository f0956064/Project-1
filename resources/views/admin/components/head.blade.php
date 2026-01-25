<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>{{ \Config::get('settings.company_name') }} - Admin</title>
    @if(\Config::get('app.env') == 'local')
        <meta name="robots" content="noindex">
        <meta name="robots" content="nofollow">
    @endif
    <!-- Favicon-->
    <link rel="icon" href="{{ asset('assets/favicon.ico')}}" type="image/x-icon">

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('admin-form-plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" />

    <!-- Bootstrap DatePicker Css -->
    <link href="{{ asset('admin-form-plugins/bootstrap-datepicker/css/bootstrap-datepicker.css')}}" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <link href="{{ asset('admin-form-plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/toastr/build/toastr.min.css') }}">

    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        var _token = '{{csrf_token()}}';
        var PATH = '{{url("/admin/")}}';
    </script>
    @stack('page_css')
</head>