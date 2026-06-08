{!! Form::open(['route' => ['tags.destroy', $id_kategori], 'method' => 'delete', 'id' => 'form-hapus-'.$id_kategori]) !!}
<div class='btn-group'>
    <a href="{{ route('tags.edit', $id_kategori) }}" class='btn btn-default btn-xs' title="Edit">
        <i class="glyphicon glyphicon-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapusCakep({{ $id_kategori }})">
        <i class="glyphicon glyphicon-trash"></i>
    </button>
</div>
{!! Form::close() !!}