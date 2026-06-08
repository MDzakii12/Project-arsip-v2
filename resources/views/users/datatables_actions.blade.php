{!! Form::open(['route' => ['users.destroy', $id], 'method' => 'delete', 'id' => 'form-hapus-'.$id]) !!}
<div class='btn-group'>
    <a href="{{ route('users.show', $id) }}" class='btn btn-default btn-xs' title="Lihat Detail">
        <i class="glyphicon glyphicon-eye-open"></i>
    </a>
    <a href="{{ route('users.edit', $id) }}" class='btn btn-default btn-xs' title="Edit">
        <i class="glyphicon glyphicon-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapusCakepPegawai({{ $id }})">
        <i class="glyphicon glyphicon-trash"></i>
    </button>
</div>
{!! Form::close() !!}