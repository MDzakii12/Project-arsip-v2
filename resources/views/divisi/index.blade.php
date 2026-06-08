@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Manajemen Divisi</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                
                <form action="{{ route('divisi.store') }}" method="POST" style="margin-bottom: 20px;">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="nama_divisi" class="form-control" placeholder="Masukkan Nama Divisi Baru..." required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="glyphicon glyphicon-plus"></i> Tambah Divisi
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="bg-info">
                            <th width="50px" class="text-center">ID</th>
                            <th>Nama Divisi</th>
                            <th width="100px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($divisis as $divisi)
                            <tr>
                                <td class="text-center">{{ $divisi->id_divisi }}</td>
                                <td>{{ $divisi->nama_divisi }}</td>
                                <td class="text-center">
                                    <form action="{{ route('divisi.destroy', $divisi->id_divisi) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin mau hapus divisi ini?')">
                                            <i class="glyphicon glyphicon-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data divisi. Silakan tambahkan di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection