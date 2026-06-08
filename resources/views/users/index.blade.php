@extends('layouts.app')
@section('title','Users List')
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Daftar Pegawai</h1>
        <h1 class="pull-right">
           @can('create users')
           <a class="btn btn-primary pull-right" style="margin-top: -10px; margin-bottom: 5px;" href="{!! route('users.create') !!}">
               <i class="fa fa-plus"></i>
               Tambah Pegawai
           </a> 
           @endcan
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('users.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusCakepPegawai(id) {
            Swal.fire({
                title: 'Yakin mau menghapus pegawai ini?',
                text: "Data pegawai dan akses akun login-nya bakal dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Pegawai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form otomatis kalau diklik Ya
                    document.getElementById('form-hapus-' + id).submit();
                }
            })
        }
    </script>

@endsection

