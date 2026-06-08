@extends('layouts.app')

@section('title','Daftar Kategori Arsip')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Kategori Arsip</h1>
        <h1 class="pull-right">
            @can('create tags')
                <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
                   href="{!! route('tags.create') !!}">
                    <i class="fa fa-plus"></i>
                    Tambah Kategori
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
                @include('tags.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusCakep(id) {
            Swal.fire({
                title: 'Yakin mau dihapus?',
                text: "Data yang udah dihapus nggak bisa balik lagi lho!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Aja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-hapus-' + id).submit();
                }
            })
        }
    </script>

@endsection