<div class="form-group col-sm-12">
    <label>Nama Folder:</label>
    {!! Form::text('nama_arsip', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Contoh: Folder SMP, Folder Surat Keputusan, Yona Cantik']) !!}
</div>

<div class="form-group col-sm-12">
    <label>Pilih Kategori Folder (Bisa pilih lebih dari satu):</label>
    {!! Form::select('id_kategori[]', $kategori_arsip ?? [], $selected_tags ?? null, ['class' => 'form-control select2', 'multiple' => 'multiple', 'required' => 'required']) !!}
</div>

<div class="form-group col-sm-12">
    <label>Deskripsi / Keterangan Folder:</label>
    {!! Form::textarea('deskripsi', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Tuliskan keterangan singkat folder ini (opsional)...']) !!}
</div>

@if(auth()->user()->is_super_admin)
    <div class="form-group col-sm-6">
        <label>Tugaskan ke Pegawai (Hak Akses Personal):</label>
        {!! Form::select('id_user', $pegawais ?? [], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Pegawai / Kosongkan --']) !!}
    </div>

    <div class="form-group col-sm-6">
        <label>Tugaskan ke Divisi (Hak Akses Grup):</label>
        {!! Form::select('divisi', ['Semua' => 'Semua Divisi (Publik)', 'TK' => 'TK', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA'], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Divisi / Kosongkan --']) !!}
    </div>
    <div class="clearfix"></div>
@endif

<div class="form-group col-sm-12" style="margin-top: 20px;">
    {!! Form::submit('Simpan Folder Baru', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('documents.index') !!}" class="btn btn-default">Batal</a>
</div>