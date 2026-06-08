@extends('layouts.app')
@section('title',ucfirst(config('settings.document_label_plural'))." List")
@section('css')
    <style type="text/css">
        .bg-folder-shaper {
            width: 100%;
            height: 115px;
            border-radius: 0px 15px 15px 15px !Important;
        }

        .folder-shape-top {
            width: 57px;
            height: 17px;
            border-radius: 20px 37px 0px 0px;
            position: absolute;
            top: -16px;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .widget-user-2 .widget-user-username, .widget-user-2 .widget-user-desc {
            margin-left: 10px;
            font-weight: 400;
            font-size: 17px;
        }

        .widget-user-username {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .m-t-20 {
            margin-top: 20px;
        }

        .dropdown-menu {
            min-width: 100%;
        }

        .doc-box.box {
            box-shadow: 0 0px 0px rgba(0, 0, 0, 0.0) !important;
        }

        .bg-folder-shaper:hover {
            background-color: yellow;
        }

        .select2-container {
            width: 100% !important;
        }

        #filterForm.in, filterForm.collapsing {
            display: block !important;
        }
    </style>
@stop
@section('scripts')
    <script>

    </script>
@stop
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Daftar Folder</h1>
        @if(auth()->user()->name == 'Super Admin')
        <h1 class="pull-right">
            <a href="{{route('documents.create')}}"
               class="btn btn-primary">
                <i class="fa fa-plus"></i>
                Tambah Folder
            </a>
        </h1>
        @endif
    </section>
    <div class="content" style="margin-top: 22px;">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-header">
                <div class="form-group hidden visible-xs">
                    <button type="button" class="btn btn-default btn-block" data-toggle="collapse"
                            data-target="#filterForm"><i class="fa fa-filter"></i> Filter
                    </button>
                </div>
                {!! Form::model(request()->all(), ['method'=>'get','class'=>'form-inline visible hidden-xs','id'=>'filterForm']) !!}
                <div class="form-group">
                    <label for="search" class="sr-only">Search</label>
                    {!! Form::text('search',null,['class'=>'form-control input-sm','placeholder'=>'Search...']) !!}
                </div>
                <div class="form-group">
                    <label for="tags" class="sr-only">Kategori :</label>
                    <select class="form-control select2 input-sm" name="tags[]" id="tags"
                            data-placeholder="-- Pilih Kategori --" multiple>
                        @foreach($tags as $tag)
                            @canany(['read documents','read documents in tag '.$tag->id])
                                <option
                                    value="{{$tag->id}}" {{in_array($tag->id,request('tags',[]))?'selected':''}}>{{$tag->name}}</option>
                            @endcanany
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status" class="sr-only">Status :</label>
                    {!! Form::select('status',['0'=>"ALL",config('constants.STATUS.PENDING')=>config('constants.STATUS.PENDING'),config('constants.STATUS.APPROVED')=>config('constants.STATUS.APPROVED'),config('constants.STATUS.REJECT')=>config('constants.STATUS.REJECT')],null,['class'=>'form-control input-sm']) !!}
                </div>
                <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-filter"></i> Filter</button>
                {!! Form::close() !!}
            </div>
            <div class="box-body">
                <div class="row">
                    @forelse ($documents as $document)
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 m-t-20" style="cursor:pointer;" onclick="window.location='{{ route('documents.show', $document->id_arsip) }}'">
                            <div class="doc-box box box-widget widget-user-2">
                                <div class="widget-user-header bg-gray bg-folder-shaper no-padding">
                                    <div class="folder-shape-top bg-gray"></div>
                                    <div class="box-header">
                                        <a href="{{ route('documents.show', $document->id_arsip) }}" style="color: black;">
                                            <h3 class="box-title"><i class="fa fa-folder text-yellow"></i></h3>
                                        </a>

                                        <div class="box-tools pull-right" onclick="event.stopPropagation();">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false" onclick="event.preventDefault(); event.stopPropagation(); $(this).parent().toggleClass('open');">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                                    <li>
                                                        <a href="{{ route('documents.show', $document->id_arsip) }}" style="padding: 6px 20px; color: #3c8dbc; font-weight: 500;">
                                                            <i class="fa fa-eye" style="width: 20px;"></i> Buka Folder
                                                        </a>
                                                    </li>
                                                    
                                                    @if(auth()->user()->is_super_admin)
                                                    <li>
                                                        <a href="{{ route('documents.edit', $document->id_arsip) }}" style="padding: 6px 20px; color: #f39c12; font-weight: 500;">
                                                            <i class="fa fa-edit" style="width: 20px;"></i> Edit Folder
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('documents.destroy', $document->id_arsip) }}" method="POST" class="form-hapus-folder" style="display: inline; width: 100%;">
                                                            {{ csrf_field() }}
                                                            {{ method_field('DELETE') }}
                                                            <button type="button" onclick="hapusFolderMewah(this)" class="btn btn-link" style="width: 100%; text-align: left; padding: 6px 20px; color: #dc3545; text-decoration: none; font-weight: 500;">
                                                                <i class="fa fa-trash" style="width: 20px;"></i> Hapus Folder
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('documents.show', $document->id_arsip) }}" style="color: black; display: block; padding-bottom: 10px;">
                                        <h5 class="widget-user-username" title="{{$document->nama_arsip}}" data-toggle="tooltip">
                                            <strong>{{$document->nama_arsip}}</strong>
                                        </h5>
                                        <h5 class="widget-user-desc" style="font-size: 12px; color: #777;">
                                            <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($document->created_at)->format('d M Y') }}
                                            
                                            @if($document->divisi)
                                                <span class="label label-info pull-right" style="margin-right: 15px;">{{ $document->divisi }}</span>
                                            @endif
                                        </h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12 text-center" style="padding: 60px 0;">
                            <i class="fa fa-folder-open-o" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                            <h4 style="color: #888; font-weight: bold;">Belum ada folder arsip.</h4>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="box-footer">
                {!! $documents->appends(request()->all())->render() !!}
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusFolderMewah(button) {
            Swal.fire({
                title: 'Konfirmasi Hapus Folder',
                text: "Apakah Anda yakin ingin menghapus folder ini beserta seluruh isinya? Data arsip di dalamnya akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah bahaya
                cancelButtonColor: '#3085d6', // Biru aman
                confirmButtonText: '<i class="fa fa-bomb"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kalau klik Ya, form otomatis nge-submit
                    button.closest('form').submit();
                }
            })
        }
    </script>

@endsection
