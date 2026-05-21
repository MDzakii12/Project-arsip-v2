<div class="form-group col-sm-6">
    <label>Name:</label>
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="clearfix"></div>

@if(auth()->user()->is_super_admin)
    <div class="form-group col-sm-6">
        <label>Tugaskan Arsip Ini Kepada Pegawai:</label>
        {!! Form::select('pemilik_id', $pegawais ?? [], isset($document) ? $document->created_by : null, ['class' => 'form-control', 'placeholder' => '-- Pilih Pegawai / Kosongkan --']) !!}
    </div>

    <div class="form-group col-sm-6">
        <label>Tugaskan Arsip Ini Kepada Divisi:</label>
        {!! Form::select('divisi', [
            'Paud' => 'PAUD', 
            'TK' => 'TK', 
            'SD' => 'SD', 
            'SMP' => 'SMP', 
            'SMA' => 'SMA'
        ], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Divisi / Kosongkan --']) !!}
    </div>
    
    <div class="clearfix"></div>
@endif

{{--additional Attributes--}}
@foreach ($customFields as $customField)
    <div class="form-group col-sm-6 {{ $errors->has("custom_fields.$customField->name") ? 'has-error' :'' }}">
        {!! Form::label("custom_fields[$customField->name]", Str::title(str_replace('_',' ',$customField->name)).":") !!}
        {!! Form::text("custom_fields[$customField->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($customField->suggestions),'autocomplete'=>is_array($customField->suggestions)?'off':'on']) !!}
        {!! $errors->first("custom_fields.$customField->name",'<span class="help-block">:message</span>') !!}
    </div>
@endforeach
{{--end additional attributes--}}

<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('documents.index') !!}" class="btn btn-default">Cancel</a>
</div>