<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | E-Arsip BPMP</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/lte/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lte/skins/skin-blue-light.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css">

    <style>
        body { background-color: #f4f6f9 !important; }
        .login-box-body {
            border: 1px solid rgba(0, 40, 100, 0.12) !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05) !important;
            border-radius: 8px;
            padding: 30px;
        }
        .login-logo { margin-bottom: 20px; }
        .login-box-msg {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #3c8dbc;
            border-color: #367fa9;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>

    <style>
        body.login-page {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 50%, #3c8dbc 100%) !important; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-box {
            width: 420px !important;
            margin: 0 !important;
        }


        .login-logo a {
            color: #ffffff !important;
            font-weight: 800 !important;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            font-size: 34px;
            margin-bottom: 25px;
            display: block;
        }

        .login-box-body {
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
            padding: 45px 35px !important;
            border: none !important;
            background: #ffffff !important;
        }
        
        .login-box-body .btn-primary {
            background: linear-gradient(135deg, #1e73be 0%, #15558d 100%) !important; /* Biru agak tua biar kontras */
            border: none !important;
            border-radius: 8px !important;
            padding: 14px !important;
            font-weight: 600 !important;
            letter-spacing: 0.8px;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .login-box-body .form-control {
            border-radius: 8px !important;
            padding: 22px 15px !important; 
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #334155;
            font-size: 14.5px;
            transition: all 0.3s ease;
        }
        .login-box-body .form-control:focus {
            background-color: #ffffff;
            border-color: #3c8dbc; 
            box-shadow: 0 0 0 4px rgba(60, 141, 188, 0.2) !important;
        }

        .login-box-body .btn-primary {
            background: linear-gradient(135deg, #3c8dbc 0%, #255b7a 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 14px !important;
            font-weight: 600 !important;
            letter-spacing: 0.8px;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }
        .login-box-body .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(60, 141, 188, 0.4) !important;
        }
        
        .form-control-feedback {
            line-height: 44px !important;
            color: #94a3b8 !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('home') }}"><b>E-Arsip</b> BPMP</a>
    </div>

    <div class="login-box-body text-center">
        <i class="fa fa-building" style="font-size: 40px; color: #3c8dbc; margin-bottom: 10px;"></i>
        <p class="login-box-msg">LOGIN SYSTEM E-ARSIP<br>BPMP PROVINSI BANTEN</p>
        @if(session('error'))
            <div class="alert alert-danger text-center" style="border-radius: 5px; font-weight: bold; margin-bottom: 15px;">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <form method="post" action="{{ url('/login') }}">
            @csrf

            <div class="form-group has-feedback {{ $errors->has('username') ? 'has-error' : '' }}">
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username / NIP">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                <input type="password" class="form-control" placeholder="Password" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="row">
                <div class="col-xs-8 text-left">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> Ingat Saya
                        </label>
                    </div>
                </div>
                <div class="col-xs-12" style="margin-top: 15px;">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">MASUK</button>
                </div>
            </div>
        </form>

        <div style="margin-top: 15px;">
            <a href="{{ url('/password/reset') }}">Lupa Password?</a><br>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    });
</script>
</body>
</html>