@extends('layouts.app')
@section('title',"Add ".ucfirst(config('settings.document_label_singular')))
@section('content')
    <section class="content-header">
        <h1>
            Tambah Folder Baru
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'documents.store', 'files' => true]) !!}
                        @include('documents.fields',['document'=>null])
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
