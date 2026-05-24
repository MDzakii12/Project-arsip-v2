<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') | {{config('settings.system_title')}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
    <link rel="stylesheet" href="{{asset('css/lte/AdminLTE.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/lte/skins/skin-blue-light.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{asset('vendor/bootstrap-wysihtml5/css/bootstrap3-wysihtml5.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/bootstrap-tagsinput/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('css/digidocu-custom.css')}}">
    @yield('css')



    <style>
        .box {
            border-radius: 12px !important;
            border-top: none !important; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }
        
        .btn {
            border-radius: 6px !important;
            font-weight: 600 !important;
            letter-spacing: 0.3px;
            transition: all 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 6px !important;
            border: 1px solid #dce1e5;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.15) !important;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 8px; 
        }
        .table tr {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-radius: 8px;
        }
        .table td, .table th {
            border: none !important;
            vertical-align: middle !important;
        }

        .box-body .table, .table-responsive .table {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .table > thead > tr > th {
            background-color: #f8fafc !important; 
            color: #64748b !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 14px 15px !important;
            border-top: none !important;
        }

        .table > tbody > tr > td {
            padding: 14px 15px !important;
            vertical-align: middle !important;
            border-top: 1px solid #f1f5f9 !important;
            color: #334155;
            font-size: 13px;
        }

        .table > tbody > tr {
            transition: all 0.2s ease;
        }
        .table > tbody > tr:hover {
            background-color: #f8fafc !important;
            box-shadow: inset 4px 0 0 0 #3c8dbc;
        }

        .label, .badge {
            padding: 5px 12px !important;
            border-radius: 20px !important; 
            font-weight: 600;
            font-size: 11px;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .main-sidebar {
            box-shadow: 2px 0 15px rgba(0,0,0,0.03) !important; 
        }
        .sidebar-menu > li > a {
            border-radius: 8px !important;
            margin: 4px 12px !important; 
            padding: 12px 15px !important;
            transition: all 0.3s ease;
        }
        .sidebar-menu > li:hover > a, .sidebar-menu > li.active > a {
            background-color: #3c8dbc !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(60, 141, 188, 0.3) !important; 
            transform: translateX(4px); 
        }
        .dataTables_filter input {
            border-radius: 20px !important; 
            padding: 5px 15px !important;
            border: 1px solid #dce1e5 !important;
            outline: none !important;
            transition: all 0.3s;
        }
        .dataTables_filter input:focus {
            box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.15) !important;
            border-color: #3c8dbc !important;
        }

        .pagination > li > a, .pagination > li > span {
            border-radius: 50% !important; 
            margin: 0 4px !important;
            border: none !important;
            color: #64748b;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            width: 35px;
            height: 35px;
            line-height: 22px;
            text-align: center;
            transition: all 0.2s;
        }
        .pagination > .active > a, .pagination > .active > span {
            background-color: #3c8dbc !important;
            box-shadow: 0 4px 10px rgba(60, 141, 188, 0.4) !important;
            color: white !important;
            transform: scale(1.1); 
        }

        .form-group {
            margin-bottom: 22px !important; 
        }
        
        .form-group label {
            font-weight: 600 !important;
            color: #475569 !important; 
            margin-bottom: 8px !important;
            font-size: 13.5px !important;
            letter-spacing: 0.3px;
        }
        
        .form-group .form-control {
            padding: 12px 15px !important;
            height: auto !important;
            font-size: 14px !important;
            background-color: #f8fafc !important; 
            border: 1px solid #cbd5e1 !important;
            transition: all 0.3s ease;
        }
        
        .form-group .form-control:focus {
            background-color: #ffffff !important; 
            border-color: #3c8dbc !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; 
        }

        .select2-container--default .select2-selection--multiple, 
        .select2-container--default .select2-selection--single {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background-color: #f8fafc !important;
            min-height: 45px !important;
            padding: 5px !important;
        }
        
        .box-footer, .form-group .btn {
            margin-top: 10px;
            padding: 10px 20px !important;
        }
       
        .skin-blue-light .main-header .logo {
            background-color: #ffffff !important; 
            color: #3c8dbc !important; 
            font-weight: 800 !important;
            letter-spacing: 0.5px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05) !important; 
            border-bottom: 1px solid #f1f5f9 !important;
            transition: all 0.3s;
        }
        
        .skin-blue-light .main-header .logo:hover {
            background-color: #f8fafc !important;
        }

        .skin-blue-light .main-header .navbar {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            box-shadow: 0 4px 15px rgba(0, 242, 254, 0.15) !important;
            border: none !important;
        }

        .skin-blue-light .main-header .navbar .sidebar-toggle,
        .skin-blue-light .main-header .navbar .nav > li > a {
            color: #ffffff !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .skin-blue-light .main-header .navbar .sidebar-toggle:hover,
        .skin-blue-light .main-header .navbar .nav > li > a:hover {
            background-color: rgba(255, 255, 255, 0.2) !important; /* Putih transparan */
            color: #ffffff !important;
        }
        /* 11. KOSMETIK PENYELAMAT SAAT MENU KIRI DIPERKECIL (COLLAPSE) */
        
        /* Menormalkan kembali jarak menu agar ikon tidak terjepit */
        body.sidebar-collapse .sidebar-menu > li > a {
            margin: 0 !important; 
            border-radius: 0 !important; /* Menghilangkan lengkungan agar pas dengan lebar 50px */
            transform: none !important; /* Mematikan efek lompat ke kanan */
            text-align: center !important;
        }

        /* Merapikan posisi kotak logo (huruf E) saat diperkecil */
        body.sidebar-collapse .main-header .logo {
            width: 50px !important;
            padding: 0 !important;
            text-align: center !important;
            overflow: hidden;
        }

        body.sidebar-collapse .sidebar-menu > li:hover > a, 
        body.sidebar-collapse .sidebar-menu > li.active > a {
            border-radius: 0 !important;
            margin: 0 !important;
            width: 50px !important;
        }

        .dataTables_empty {
            padding: 50px 20px !important;
            text-align: center !important;
            color: #64748b !important; 
            font-size: 15px !important;
            font-weight: 500 !important;
            background-color: #f8fafc !important;
            border-radius: 12px !important;
            border: 2px dashed #cbd5e1 !important; 
            transition: all 0.3s ease;
        }

        .dataTables_empty::before {
            content: "\f07c"; 
            font-family: "FontAwesome";
            display: block;
            font-size: 45px;
            color: #cbd5e1; 
            margin-bottom: 12px;
            transition: transform 0.3s;
        }

        .dataTables_empty:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
        }
        .dataTables_empty:hover::before {
            transform: scale(1.1);
            color: #94a3b8;
        }

        .content input[placeholder="Search..."], 
        .content .select2-container--default .select2-selection--single {
            border-radius: 20px !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02) !important;
            height: 38px !important;
        }
        
        .content .btn-default { 
            border-radius: 20px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            font-weight: 600 !important;
            color: #475569 !important;
            padding: 8px 22px !important;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) !important;
        }
        
        .content .btn-default:hover {
            background-color: #f1f5f9 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important;
            transform: translateY(-2px);
        }

        .box-body > .row > div {
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            margin-bottom: 20px; 
        }
        
        .box-body > .row > div:hover {
            transform: translateY(-8px) scale(1.02); 
            z-index: 10;
            position: relative;
        }
        
        .box-body > .row > div > div {
            box-shadow: 0 4px 12px rgba(0,0,0,0.04) !important;
            transition: all 0.3s ease !important;
            border: 1px solid #e2e8f0; 
        }
        
        .box-body > .row > div:hover > div {
            box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
        }
        .nav-tabs-custom {
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
            border: none !important;
            padding: 10px !important;
            background: #ffffff !important;
        }
        .nav-tabs {
            border-bottom: 2px solid #f1f5f9 !important;
            margin-bottom: 20px !important;
        }
        .nav-tabs > li > a {
            color: #64748b !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease;
        }
        .nav-tabs > li.active > a, 
        .nav-tabs > li.active > a:hover, 
        .nav-tabs > li > a:hover {
            color: #0072ff !important; 
            background-color: transparent !important;
            border-bottom: 3px solid #0072ff !important;
            border-radius: 8px 8px 0 0 !important;
        }
        .tab-content .row > div > div {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            overflow: hidden !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            transition: all 0.3s ease !important;
            background-color: #ffffff !important;
        }
        .tab-content .row > div > div:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        .tab-content .row > div > div .bg-blue,
        .tab-content .row > div > div [style*="background-color"] {
            background: #ffffff !important; 
            border-top: 1px solid #f1f5f9 !important;
            padding: 15px !important;
        }
        .tab-content .row > div > div .bg-blue *,
        .tab-content .row > div > div [style*="background-color"] * {
            color: #334155 !important; 
        }
        .tab-content .row > div > div h5,
        .tab-content .row > div > div b {
            font-weight: 700 !important;
            font-size: 15px !important;
            color: #0f172a !important;
            margin-bottom: 5px !important;
        }
        .tab-content .row > div > div .label {
            background-color: #e0f2fe !important;
            color: #0284c7 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            display: inline-block;
            margin-bottom: 10px;
        }
       
        .tab-content .custom-box .box-header {
            background-color: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 15px !important;
        }

        .tab-content .custom-box .box-header * {
            color: #334155 !important;
        }

        .tab-content .custom-box .box-header .fa-ellipsis-v {
            color: #475569 !important; 
            font-size: 18px !important;
            cursor: pointer !important;
        }

        .tab-content .custom-box .box-header .btn-default {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
       
        .tab-content .row > div > div {
            overflow: visible !important; 
        }

        
        .tab-content .custom-box .box-header .pull-right,
        .tab-content .custom-box .box-header .box-tools,
        .tab-content .custom-box .box-header .btn {
            position: relative !important;
            z-index: 999 !important;
            pointer-events: auto !important;
        }

        
        .tab-content .custom-box .dropdown-menu {
            border-radius: 10px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
            border: none !important;
            z-index: 9999 !important; 
        }
    </style>
</head>

<body class="skin-blue-light sidebar-mini">
@if (!Auth::guest())
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="{{route('admin.dashboard')}}" class="hidden-xs logo">
                <span class="logo-mini"><b>{{config('settings.system_title')[0]}}</b></span>
                <span class="logo-lg"><b>{{config('settings.system_title')}}</b></span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <span style="display: inline-block;width: 71vw;text-align: center;font-size: 20px;line-height: 50px;color: white;" class="visible-xs-inline-block">
                    <b>{{config('settings.system_title')}}</b>
                </span>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="{{asset(config('settings.system_logo'))}}"
                                     class="user-image" alt="User Image"/>
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">{!! Auth::user()->name !!}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="{{asset(config('settings.system_logo'))}}"
                                         class="img-circle" alt="User Image"/>
                                    <p>
                                        {!! Auth::user()->name !!}
                                        <small>Member since {!! Auth::user()->created_at->format('M. Y') !!}</small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{route('profile.manage')}}" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{!! url('/logout') !!}" class="btn btn-default btn-flat"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Log out
                                        </a>
                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                              style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
    @include('layouts.sidebar')
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
@else
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{!! url('/') !!}">
                    InfyOm Generator
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="{!! url('/home') !!}">Home</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <li><a href="{!! url('/login') !!}">Login</a></li>
                    <li><a href="{!! url('/register') !!}">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endif
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script src="{{asset('vendor/bootstrap-typeahead/js/bootstrap3-typeahead.min.js')}}"></script>
<script src="{{asset('vendor/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{asset('vendor/bootstrap-wysihtml5/js/bootstrap3-wysihtml5.all.min.js')}}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.4.2/handlebars.min.js"></script>
<script src="{{asset('js/handlebar-helpers.js')}}"></script>
<script src="{{asset('js/digidocu-custom.js')}}"></script>
@yield('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var alertSukses = $('.alert-success');
            if (alertSukses.length > 0) {
                var pesan = alertSukses.text().replace('×', '').trim();
                alertSukses.hide(); 
                
                
                Swal.fire({
                    icon: 'success',
                    title: '',
                    text: pesan,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    backdrop: `rgba(0,0,0,0.2)` 
                });
            }

            var alertError = $('.alert-danger');
            
            if (alertError.length > 0) {
                var pesanErr = alertError.text().replace('×', '').trim();
                alertError.hide();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Gagal!',
                    text: pesanErr,
                    confirmButtonColor: '#3c8dbc'
                });
            }
        });
    </script>
    <script>
        function hapusFolder(tombol) {
            var form = $(tombol).closest('form');
            
            Swal.fire({
                title: 'Hapus Folder Ini?',
                text: "Data yang dihapus tidak bisa dikembalikan lho!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', 
                cancelButtonColor: '#6c757d', 
                confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); 
                }
            });
        }
    </script>
</body>
</html>
