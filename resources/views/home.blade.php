@extends('layouts.app')
@section('title','Dashboard')

@section('css')
<style>
    .info-box, .small-box {
        border-radius: 16px !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06) !important;
        border: none !important;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important;
    }
    
    .info-box:hover, .small-box:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
    }

    .bg-aqua, .bg-blue { 
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%) !important; 
        color: white !important; 
    }
    .bg-green { 
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; 
        color: white !important; 
    }
    .bg-yellow, .bg-orange { 
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%) !important; 
        color: white !important; 
    }
    .bg-red { 
        background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%) !important; 
        color: white !important; 
    }

    
    .small-box .icon, .info-box-icon {
        color: rgba(255, 255, 255, 0.25) !important; 
    }
    
    
    .info-box-text, .info-box-number, .small-box h3, .small-box p {
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1); 
    }
</style>
@endsection

@section('content')

@if(auth()->user()->is_super_admin)
    <section class="content-header">
        <h1>Dashboard <small>Statistik & Informasi E-Arsip</small></h1>
    </section>
    
    <section class="content" style="margin-top: 15px;">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-folder-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Arsip</span>
                        <span class="info-box-number">{{ $documentCounts }}</span>
                        <span class="progress-description">Keseluruhan Dokumen</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-file-pdf-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total File</span>
                        <span class="info-box-number">{{ $filesCounts }}</span>
                        <span class="progress-description">Keseluruhan File</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pegawai Aktif</span>
                        <span class="info-box-number">{{ $pegawaiAktif }}</span>
                        <span class="progress-description">User Terdaftar</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-cloud-upload"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Arsip Hari Ini</span>
                        <span class="info-box-number">{{ $arsipHariIni }}</span>
                        <span class="progress-description">Baru Diunggah</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Statistik Kategori Arsip</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="kategoriChart" style="height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border text-center">
                        <h3 class="box-title"><i class="fa fa-upload"></i> Quick Upload File</h3>
                    </div>
                    <div class="box-body">
                        <form action="#" class="text-center" onsubmit="return gotoUpload()">
                            <div class="form-group">
                                <label for="">Pilih Arsip Tujuan:</label>
                                <select name="document_id" id="document_id" class="form-control select2" required>
                                    <option value="">-- Pilih Dokumen Arsip --</option>
                                    @foreach ($documents as $document)
                                        @can('view',$document)
                                            <option value="{{$document->id}}">{{$document->name}}</option>
                                        @endcan
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-block"><i class="fa fa-cloud-upload"></i> Lanjut Upload File</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-warning">
                    <div class="box-header no-border">
                        <h3 class="box-title"><i class="fa fa-history"></i> Log Aktivitas Terakhir</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="timeline">
                            @php $currentDate = ''; @endphp
                            @foreach ($activities as $activity)
                                @can('view', $activity->document)
                                    @php $actDate = formatDate($activity->created_at, 'd M Y'); @endphp
                                    @if($currentDate != $actDate)
                                        <li class="time-label">
                                            <span class="bg-red">{{ $actDate }}</span>
                                        </li>
                                        @php $currentDate = $actDate; @endphp
                                    @endif
                                    <li>
                                        <i class="fa fa-user bg-aqua" data-toggle="tooltip" title="{{$activity->createdBy->name}}"></i>
                                        <div class="timeline-item">
                                            <span class="time" data-toggle="tooltip" title="{{formatDateTime($activity->created_at)}}">
                                                <i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($activity->created_at)->diffForHumans()}}
                                            </span>
                                            <h3 class="timeline-header no-border">
                                                <strong>{{$activity->createdBy->name}}</strong> {!! $activity->activity !!}
                                            </h3>
                                        </div>
                                    </li>
                                @endcan
                            @endforeach
                            <li><i class="fa fa-clock-o bg-gray"></i></li>
                        </ul>
                        <div class="text-center">
                            {!! $activities->appends(request()->all())->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@else
    <section class="content-header" style="padding-bottom: 20px;">
        <h1 style="font-size: 26px; font-weight: bold; color: #333; text-transform: uppercase;"><i class="fa fa-archive"></i> Dashboard Arsip Pribadi</h1>
        <p style="color: #666; margin-top: 5px;">Selamat datang, {{ auth()->user()->name }}. Temukan dan kelola arsip Anda di sini.</p>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                
                <form action="{{ route('documents.index') }}" method="GET" style="margin-bottom: 30px;">
                    <div class="input-group" style="box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px;">
                        <input type="text" name="search" class="form-control input-lg" placeholder="🔍 Cari nama dokumen atau arsip Anda di sini..." style="height: 60px; font-size: 18px; border: none; border-radius: 8px 0 0 8px; padding-left: 20px;">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-lg" style="height: 60px; padding: 0 35px; font-size: 18px; border-radius: 0 8px 8px 0; font-weight: bold;">
                                Cari
                            </button>
                        </span>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="info-box" style="border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 10px;">
                            <span class="info-box-icon bg-blue" style="border-radius: 8px;"><i class="fa fa-folder-open"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-size: 16px; text-transform: none; color: #777;">Kumpulan Dokumen</span>
                                <span class="info-box-number" style="font-size: 28px; color: #333;">{{ $documentCounts }} <small style="font-size: 14px; color: #999;">Folder</small></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="info-box" style="border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 10px;">
                            <span class="info-box-icon bg-green" style="border-radius: 8px;"><i class="fa fa-file-text"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-size: 16px; text-transform: none; color: #777;">Total Lampiran File</span>
                                <span class="info-box-number" style="font-size: 28px; color: #333;">{{ $documents->sum(function($d) { return $d->files->count(); }) }} <small style="font-size: 14px; color: #999;">File</small></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-solid" style="border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 15px;">
                    <div class="box-header with-border" style="padding: 15px 20px; border-bottom: 1px solid #eee;">
                        <h3 class="box-title" style="font-weight: bold; color: #444;"><i class="fa fa-clock-o text-muted" style="margin-right: 8px;"></i> Akses Terakhir / Terbaru</h3>
                        <a href="{{ route('documents.index') }}" class="btn btn-default btn-sm pull-right" style="border-radius: 20px;">Lihat Semua Folder</a>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-stacked">
                            @forelse($documents->take(5) as $doc)
                                <li>
                                    <a href="{{ route('documents.show', $doc->id) }}" style="padding: 15px 20px; font-size: 16px; color: #444; display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <i class="fa fa-file-pdf-o text-danger" style="font-size: 20px; margin-right: 15px; width: 20px; text-align: center;"></i> 
                                            <strong>{{ $doc->name }}</strong>
                                        </div>
                                        <span class="text-muted" style="font-size: 13px;"><i class="fa fa-calendar"></i> {{ $doc->created_at->format('d M Y') }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center" style="padding: 40px; color: #888;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" style="width: 80px; opacity: 0.5; margin-bottom: 15px;" alt="Empty">
                                    <br>Belum ada arsip yang ditugaskan kepada Anda.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif

@endsection

@section('scripts')
@if(auth()->user()->is_super_admin)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function gotoUpload() {
            var docId = $("#document_id").val();
            if(!docId) return false;
            var urlToUp = "{{route('documents.files.create', '')}}"+"/"+docId;
            window.location.href = urlToUp;
            return false;
        }

        $(function() {
            var ctx = document.getElementById('kategoriChart').getContext('2d');
            var chartLabels = {!! $chartLabels !!};
            var chartData = {!! $chartData !!};

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Arsip',
                        data: chartData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
@endif
@stop