<div class="form-group col-sm-6">
    {!! Form::label('nama_kategori', 'Nama Kategori:') !!}
    {!! Form::text('nama_kategori', null, ['class' => 'form-control', 'maxlength' => 30, 'placeholder' => 'Contoh : Surat Masuk', 'required']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('label_warna', 'Warna Label (Opsional):') !!}
    {!! Form::input('color', 'label_warna', '#3c8dbc', ['class' => 'form-control', 'style' => 'height: 34px; padding: 2px; cursor: pointer;']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::submit('Simpan', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tags.index') }}" class="btn btn-default">Batal</a>
</div>