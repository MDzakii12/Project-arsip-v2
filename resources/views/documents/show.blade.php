@extends('layouts.app')
@section('title',"Show ".ucfirst(config('settings.document_label_singular')))
@section('css')
    <style>
        .box.custom-box {
            border: 1px solid #3c8dbc;
            box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.08)
        }

        .box.custom-box .box-header {
            background-color: #3c8dbc;
            color: #fff;
            padding: 3px 5px;
        }

        .custom-box .user-block > .username, .custom-box .user-block > .description {
            margin-left: 0;
        }

        .custom-box .box-body img {
            height: 145px;
            object-fit: contain;
            width: 100%;
            border-radius: 3px;
        }

        object.obj-file-box {
            height: 80vh;
            object-fit: contain;
            width: 100%;
            border: 1px solid rgba(0, 40, 100, 0.2);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .img-d-select .icheckbox_square-blue{
            position: absolute;
            right: 0;
            top: 0;
        }

        #sticky_footer {
            position: fixed;
            bottom: -4px;
            right: 10px;
        }
    </style>
@stop
@section('scripts')
    <script src="https://cdn.scaleflex.it/plugins/filerobot-image-editor/3/filerobot-image-editor.min.js"></script>
    <script>
    function editFileModal(file) {
        $('#edit_file_name').val(file.name);
        $('#edit_file_status').val(file.status || 'Active'); 
        $('#edit_file_lokasi_hard_copy').val(file.lokasi_hard_copy);
        
        if(file.masa_guna) {
            var tanggal = file.masa_guna.split(' ')[0]; 
            $('#edit_file_masa_guna').val(tanggal);
        } else {
            $('#edit_file_masa_guna').val('');
        }

        aturMasaGunaEdit();

        var updateUrl = "{{ url('admin/files-update') }}/" + file.id;
        $('#formEditFile').attr('action', updateUrl);

        $('#modalEditFile').modal('show');
    }

    function aturMasaGunaEdit() {
        var status = $('#edit_file_status').val();
        var kotakMasaGuna = $('#edit_file_masa_guna');

        if (status === 'Nonactive') {
            kotakMasaGuna.val(''); 
            kotakMasaGuna.prop('readonly', true); 
        } else {
            kotakMasaGuna.prop('readonly', false); 
        }
    }
</script>
    <script id="file-modal-template" type="text/x-handlebars-template">
        <div id="fileModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@{{name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-3">
                            <div class="form-group">
                                <a href="{{\Illuminate\Support\Str::finish(route('files.showfile',['dir'=>'original']),"/")}}@{{file}}?force=true"
                                   download class="btn btn-primary"><i
                                        class="fa fa-download"></i> Download original
                                </a>
                            </div>
                            <div class="form-group">
                                <label>{{ucfirst(config('settings.file_label_singular'))." Type"}}</label>
                                <p>@{{file_type.name}}</p>
                            </div>
                            <div class="form-group">
                                <label>Uploaded By:</label>
                                <p>
                                    @{{created_by.name}}
                                </p>
                            </div>
                            <div class="form-group">
                                <label>Status Surat:</label>
                                <p>
                                    <span class="label label-info">@{{status}}</span>
                                </p>
                            </div>
                            <div class="form-group">
                                <label>Masa Guna:</label>
                                <p>@{{masa_guna}}</p>
                            </div>
                            <div class="form-group">
                                <label>Lokasi Hard Copy:</label>
                                <p>@{{lokasi_hard_copy}}</p>
                            </div>
                            <div class="form-group">
                                <label>Uploaded On:</label>
                                <p>@{{formatDate created_at}}</p>
                            </div>
                            @{{#each custom_fields}}
                            <div class="form-group">
                                <label>@{{titleize @key}}</label>
                                <p>@{{this}}</p>
                            </div>
                            @{{/each}}
                        </div>
                        <div class="col-md-9">
                            <div class="file-modal-preview">
                                <object class="obj-file-box" classid=""
                                        data="{{\Illuminate\Support\Str::finish(route('files.showfile',['dir'=>'original']),"/")}}@{{file}}">
                                </object>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i>
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </script>
    <script>
        const ImageEditor = new FilerobotImageEditor();

        function showFileModal(data) {
            var template = Handlebars.compile($("#file-modal-template").html());
            var html = template(data);
            $("#modal-space").html(html);
            $("#fileModal").modal('show');

        }

        function submitPdfForm(varient){
            $("input[name='images_varient']").val(varient);
            $("#frm_image2pdf").submit();
        }

        $(function () {
            $("input[name='topdf_check[]']").on('ifToggled', function(event){
                var selectedValues = $("input[name='topdf_check[]']:checked").map(function(){
                    return $(this).val();
                }).toArray();
                if(selectedValues.length>0){
                    $("#sticky_footer").show();
                }else{
                    $("#sticky_footer").hide();
                }
                $("input[name='images']").val(selectedValues.join());
            });
            $("input[name='topdf_check[]']").trigger('ifToggled');
        });
    </script>
@stop
@section('content')
    <div id="modal-space">
    </div>
    <section class="content-header" style="margin-bottom: 27px;">
        <h1 class="pull-left">
            Detail Folder
            <small>{{$document->nama_arsip}}</small>
        </h1>
        <h1 class="pull-right" style="margin-bottom: 5px;">
            <div class="dropdown" style="display: inline-block">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i
                        class="fa fa-download"></i> Download Zip
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{route('files.downloadZip',['dir'=>'all','id'=>$document->id_arsip])}}">All</a>
                    </li>
                    <li>
                        <a href="{{route('files.downloadZip',['dir'=>'original','id'=>$document->id_arsip])}}">Original</a>
                    </li>
                    @foreach (explode(",",config('settings.image_files_resize')) as $varient)
                        <li>
                            <a href="{{route('files.downloadZip',['dir'=>$varient,'id'=>$document->id_arsip])}}">{{$varient}}w
                                (Images Only)</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @if(auth()->user()->is_super_admin)
                <a href="{{route('documents.edit', $document->id_arsip)}}" class="btn btn-primary"><i class="fa fa-edit"></i>
                    Edit</a>
                {!! Form::open(['route' => ['documents.destroy', $document->id_arsip], 'method' => 'delete', 'style'=>'display:inline;']) !!}
                <button class="btn btn-danger" onclick="return confirm('Yakin mau hapus folder ini?')" type="submit"><i
                        class="fa fa-trash"></i>
                    Delete
                </button>
                {!! Form::close() !!}
            @endif
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-3">
                <div class="box box-primary custom-box">
                    <div class="box-header">
                        <h3 class="box-title" style="font-size: 16px;"><i class="fa fa-info-circle"></i> Info Folder</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Nama Folder:</label>
                            <p style="font-size: 16px; font-weight: bold; color: #3c8dbc;">{{$document->nama_arsip}}</p>
                        </div>
                        <div class="form-group">
                            <label>Kategori:</label>
                            <p>
                                @foreach ($document->tags as $tag)
                                    <small class="label" style="background-color: {{ $tag->label_warna ?? '#fbbc04' }}; color: #fff; margin-right: 3px; display: inline-block; margin-bottom: 3px;">
                                        <i class="fa fa-folder"></i> {{$tag->nama_kategori}}
                                    </small>
                                @endforeach
                            </p>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi:</label>
                            <p>{!! $document->deskripsi ?? '<i class="text-muted">Tidak ada deskripsi</i>' !!}</p>
                        </div>
                        <div class="form-group">
                            <label>Hak Akses Personal:</label>
                            <p><i class="fa fa-user"></i> {{ $document->createdBy->name ?? 'Semua Pegawai' }}</p>
                        </div>
                        @if($document->divisi)
                        <div class="form-group">
                            <label>Hak Akses Divisi:</label>
                            <p><i class="fa class="fa fa-users"></i> <span class="label label-info">{{ $document->divisi }}</span></p>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Dibuat Pada:</label>
                            <p><i class="fa fa-calendar"></i> {!! formatDateTime($document->created_at) !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_files" data-toggle="tab" aria-expanded="true"><i class="fa fa-folder-open"></i> Isi Folder</a></li>
                        <li class=""><a href="#tab_activity" data-toggle="tab" aria-expanded="false"><i class="fa fa-history"></i> Riwayat Aktivitas</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_files">
                            
                            <div id="level2-folders" class="row" style="margin-bottom: 20px;">
                                @foreach($document->tags as $tag)
                                <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
                                    <div class="box custom-box" onclick="openFolder('{{ $tag->nama_kategori }}')" 
                                        style="cursor: pointer; border-radius: 8px; border: 1px solid #dadce0; padding: 12px 15px; display: flex; align-items: center; background: #fff; transition: background 0.2s; margin-bottom: 0;"
                                        onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                                        <i class="fa fa-folder" style="font-size: 30px; color: #fbbc04; margin-right: 15px;"></i>
                                        <span style="font-weight: 600; color: #3c4043; font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $tag->nama_kategori }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div id="level3-files" style="display: none;">
                                <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
                                    <button type="button" class="btn btn-default" onclick="closeFolder()" style="border-radius: 8px; padding: 6px 15px;">
                                        <i class="fa fa-arrow-left"></i> Kembali ke Kategori
                                    </button>
                                    @if(auth()->user()->is_super_admin || auth()->user()->jabatan == 'Operator')
                                    <a href="{{ url('admin/files-upload/'.$document->id_arsip) }}" class="btn btn-primary" style="border-radius: 8px; padding: 6px 15px;">
                                        <i class="fa fa-cloud-upload"></i> Upload File ke Kategori Ini
                                    </a>
                                    @endif
                                </div>
                                <h4 id="nama-folder-aktif" style="margin-bottom: 20px; font-weight: bold; color: #3c4043; border-bottom: 2px solid #3c8dbc; padding-bottom: 10px;">📂 Isi Arsip: </h4>
                                
                                <div class="row">
                                    @forelse ($document->files->sortBy('file_type_id') as $file)
                                        <div class="col-xs-6 col-md-4 col-lg-4 file-item" data-folder="{{ $file->fileType->nama_kategori ?? 'Uncategorized' }}">
                                            <div class="box custom-box" style="margin-bottom: 20px; border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                
                                                <div class="box-body text-center" style="padding: 10px; background: #3c8dbc;">
                                                    <img onclick="showFileModal({{json_encode($file)}})"
                                                        style="cursor:pointer; max-width: 100%; height: 150px; object-fit: cover;"
                                                        src="{{buildPreviewUrl($file->file)}}"
                                                        alt="">
                                                </div>

                                                <div class="box-header" style="background: #fff; color: #333; padding: 15px; position: relative;">
                                                    <div class="user-block" style="width: 85%; display: inline-block;">
                                                        <span class="username" style="cursor:pointer; color: #3c8dbc; display: block; font-size: 16px; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                                            onclick="showFileModal({{json_encode($file)}})">{{$file->name}}</span>
                                                        <small class="description text-gray" style="display: block; font-size: 12px; margin-top: 5px;">Diupload {{\Carbon\Carbon::parse($file->created_at)->diffForHumans()}}</small>
                                                    </div>

                                                    @if(auth()->user()->is_super_admin || auth()->user()->jabatan == 'Operator')
                                                    <div class="box-tools" style="position: absolute; top: 15px; right: 10px;">
                                                        <div class="dropdown">
                                                            <button class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown" style="padding: 5px; background: transparent; border: none;">
                                                                <i class="fa fa-ellipsis-v" style="font-size: 18px; color: #888;"></i>
                                                            </button>
                                                            <ul class="dropdown-menu pull-right" role="menu" style="min-width: 120px; border-radius: 5px; border: 1px solid #ccc; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                                                <li>
                                                                    <form action="{{ url('admin/files-upload', $file->id) }}" method="POST" class="form-hapus-file">
                                                                        {{ csrf_field() }}
                                                                        {{ method_field('DELETE') }}
                                                                        <button type="button" onclick="hapusFileMewah(this)" style="background: none; border: none; color: #dd4b39; padding: 10px 15px; width: 100%; text-align: left;">
                                                                            <i class="fa fa-trash"></i> Hapus File
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-md-12 text-center" style="padding: 40px 0; color: #999;">
                                            <i class="fa fa-file-pdf-o" style="font-size: 40px; margin-bottom: 10px;"></i>
                                            <p>Belum ada file di kategori ini.</p>
                                        </div>
                                    @endforelse
                                </div> 
                            </div>

                            <script>
                                function openFolder(tagName) {
                                    // 1. Sembunyikan folder, munculkan papan file
                                    document.getElementById('level2-folders').style.display = 'none';
                                    document.getElementById('level3-files').style.display = 'block';
                                    document.getElementById('nama-folder-aktif').innerText = '📂 Isi Kategori: ' + tagName;

                                    // 2. Mesin Sortir: Cek KTP masing-masing file
                                    let files = document.querySelectorAll('.file-item');
                                    let adaFile = false;
                                    files.forEach(function(file) {
                                        let fileKTP = file.getAttribute('data-folder');
                                        if (fileKTP && fileKTP.toLowerCase() === tagName.toLowerCase()) {
                                            file.style.display = 'block';
                                            adaFile = true;
                                        } else {
                                            file.style.display = 'none'; 
                                        }
                                    });
                                }

                                function closeFolder() {
                                    document.getElementById('level3-files').style.display = 'none';
                                    document.getElementById('level2-folders').style.display = 'flex';
                                }
                            </script>
                        </div>
                        
                        <div class="tab-pane" id="tab_activity">
                            <ul class="timeline">
                                <li class="time-label">
                                    <span class="bg-red">{{formatDate($document->updated_at,'d M Y')}}</span>
                                </li>
                                @if($document->activities)
                                    @foreach ($document->activities as $activity)
                                        <li>
                                            <i class="fa fa-user bg-aqua" title="{{$activity->createdBy->name ?? 'System'}}"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($activity->created_at)->diffForHumans()}}</span>
                                                <h4 class="timeline-header no-border">{!! $activity->activity !!}</h4>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <i class="fa fa-info bg-blue"></i>
                                        <div class="timeline-item">
                                            <h4 class="timeline-header no-border" style="padding: 10px;">Belum ada riwayat aktivitas.</h4>
                                        </div>
                                    </li>
                                @endif
                                <li><i class="fa fa-clock-o bg-gray"></i></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="sticky_footer" style="display:none;">
        <form id="frm_image2pdf" action="{{route('files.downloadPdf')}}" method="post" style="display: inline">
            @csrf
            <input type="hidden" name="images">
            <input type="hidden" name="images_varient">
            <div class="dropup">
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-file-pdf-o"></i> Convert PDF <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" onclick="submitPdfForm('original')">Original</a></li>
                    @foreach (explode(',',config('settings.image_files_resize')) as $varient)
                        <li><a href="javascript:void(0);" onclick="submitPdfForm('{{$varient}}')">{{$varient}}w</a></li>
                    @endforeach
                </ul>
            </div>
        </form>
    </div>
    
    <div class="modal fade" id="modalEditFile" tabindex="-1" role="dialog" aria-labelledby="modalEditFileLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalEditFileLabel">Edit Detail File</h4>
                </div>
                <form id="formEditFile" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama File</label>
                            <input type="text" class="form-control" name="name" id="edit_file_name" required>
                        </div>
                        <div class="form-group">
                            <label>Status Surat</label>
                            <select name="status" id="edit_file_status" class="form-control" onchange="aturMasaGunaEdit()">
                                <option value="Active">Active</option>
                                <option value="Nonactive">Nonactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Masa Guna</label>
                            <input type="date" class="form-control" name="masa_guna" id="edit_file_masa_guna">
                        </div>
                        <div class="form-group">
                            <label>Lokasi Hard Copy</label>
                            <input type="text" class="form-control" name="lokasi_hard_copy" id="edit_file_lokasi_hard_copy" placeholder="Contoh: Lemari A, Rak 2">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusFileMewah(button) {
            Swal.fire({
                title: 'Konfirmasi Hapus File!',
                text: "Apakah Anda yakin ingin menghapus arsip ini dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', 
                cancelButtonColor: '#3085d6', 
                confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>

@endsection
