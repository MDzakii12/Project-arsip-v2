<style>
    .box-primary .form-control:hover, 
    .box-primary .form-control:focus, 
    .box-primary .form-control:active {
        box-shadow: none !important;
        transform: none !important;
        outline: none !important;
        border: 1px solid #ced4da !important;
    }

    .box-primary select.form-control:hover, 
    .box-primary select.form-control:focus {
        box-shadow: none !important;
        transform: none !important;
    }
</style>
<div class="box box-primary">
    <div class="box-header no-border">
        <h3 class="box-title">Data Profil Pegawai</h3>
    </div>
    <div class="box-body">
        <div class="row">

            <div class="form-group col-sm-6 {{ $errors->has('name') ? 'has-error' :'' }}">
                {!! Form::label('name', 'Nama Lengkap:') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                {!! $errors->first('name','<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group col-sm-6 {{ $errors->has('nip') ? 'has-error' :'' }}">
                {!! Form::label('nip', 'NIP:') !!}
                {!! Form::text('nip', null, ['class' => 'form-control']) !!}
                {!! $errors->first('nip','<span class="help-block">:message</span>') !!}
            </div>
            <div class="clearfix"></div>

            <div class="form-group col-sm-6 {{ $errors->has('jabatan') ? 'has-error' :'' }}">
                {!! Form::label('jabatan', 'Jabatan:') !!} {!! Form::select('jabatan', [
                    'Pegawai' => 'Pegawai Biasa', 
                    'Operator' => 'Operator (Manajemen Data)',
                    'Admin' => 'Administrator (Akses Penuh)',
                    'Kepala Sub Bagian' => 'Kepala Sub Bagian',
                    'Staf' => 'Staf'
                ], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Jabatan --']) !!}
                {!! $errors->first('jabatan','<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group col-sm-6 {{ $errors->has('address') ? 'has-error' :'' }}">
                {!! Form::label('address', 'Unit Kerja:') !!}
                {!! Form::text('address', null, ['class' => 'form-control']) !!}
                {!! $errors->first('address','<span class="help-block">:message</span>') !!}
            </div>
            <div class="clearfix"></div>

            <div class="form-group col-sm-6 {{ $errors->has('no_hp') ? 'has-error' :'' }}">
                {!! Form::label('no_hp', 'Nomor HP / WhatsApp:') !!}
                {!! Form::text('no_hp', null, ['class' => 'form-control']) !!}
                {!! $errors->first('no_hp','<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group col-sm-6 {{ $errors->has('email') ? 'has-error' :'' }}">
                {!! Form::label('email', 'Email (Opsional):') !!}
                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                {!! $errors->first('email','<span class="help-block">:message</span>') !!}
            </div>
            <div class="clearfix"></div>

            <div class="form-group col-sm-6 {{ $errors->has('username') ? 'has-error' :'' }}">
                {!! Form::label('username', 'Username Login:') !!}
                {!! Form::text('username', null, ['class' => 'form-control']) !!}
                {!! $errors->first('username','<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group col-sm-6 {{ $errors->has('password') ? 'has-error' :'' }}">
                {!! Form::label('password', 'Password Login:') !!}
                {!! Form::password('password', ['class' => 'form-control']) !!}
                {!! $errors->first('password','<span class="help-block">:message</span>') !!}
            </div>
            <div class="clearfix"></div>

            <div class="form-group col-sm-6">
                {!! Form::label('divisi', 'Divisi Pegawai:') !!}
                {!! Form::select('divisi', [
                    'Paud' => 'PAUD', 
                    'TK' => 'TK', 
                    'SD' => 'SD', 
                    'SMP' => 'SMP', 
                    'SMA' => 'SMA'
                ], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Divisi --']) !!}
            </div>

            <div class="form-group col-sm-6 {{ $errors->has('status') ? 'has-error' :'' }}">
                {!! Form::label('status', 'Status Akun:') !!}
                {!! Form::select('status', [config('constants.STATUS.ACTIVE') => 'Aktif', config('constants.STATUS.BLOCK') => 'Diblokir'],null, ['class'=>'form-control']); !!}
                {!! $errors->first('status','<span class="help-block">:message</span>') !!}
            </div>

        </div>
    </div>
</div>

@can('user manage permission')
    <div style="display: none;">
        <input name="global_permissions[]" type="checkbox" value="read documents" checked>
    </div>
@endcan

<div class="form-group" style="margin-top: 20px;">
    {!! Form::submit('Simpan Data Pegawai', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('users.index') !!}" class="btn btn-default">Batal</a>
</div>
