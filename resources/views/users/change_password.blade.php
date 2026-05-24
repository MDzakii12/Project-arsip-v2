@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>Ganti Password Mandiri</h1>
</section>

<div class="content">
    <div class="clearfix"></div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Gagal!</h4>
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Sukses!</h4>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="box box-primary">
        <div class="box-header no-border">
            <h3 class="box-title">Silakan Perbarui Kata Sandi Anda</h3>
        </div>
        
        {!! Form::open(['route' => 'password.update', 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                
                <div class="form-group col-sm-6">
                    {!! Form::label('current_password', 'Password Saat Ini / Lama:') !!}
                    {!! Form::password('current_password', ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="clearfix"></div>

                <div class="form-group col-sm-6">
                    {!! Form::label('password', 'Password Baru (Minimal 6 Karakter):') !!}
                    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="clearfix"></div>

                <div class="form-group col-sm-6">
                    {!! Form::label('password_confirmation', 'Ulangi / Konfirmasi Password Baru:') !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

            </div>
        </div>
        
        <div class="box-footer">
            {!! Form::submit('Perbarui Password', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection