<li class="{{ Request::is('admin/home*') ? 'active' : '' }}">
    <a href="{!! route('admin.dashboard') !!}"><i class="fa fa-home"></i><span>Dashboard</span></a>
</li>

{{-- Gerbang 1: Menu yang bisa dilihat ADMIN dan OPERATOR --}}
@if(auth()->user()->is_operator)
    @can('read users')
        <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
            <a href="{!! route('users.index') !!}"><i class="fa fa-users"></i><span>Manajemen Pegawai</span></a>
        </li>
    @endcan

    @can('read tags')
        <li class="{{ Request::is('admin/tags*') ? 'active' : '' }}">
            <a href="{!! route('tags.index') !!}"><i class="fa fa-tags"></i><span>Kategori Arsip</span></a>
        </li>
    @endcan

@endif

        <li class="{{ Request::is('admin/documents*') ? 'active' : '' }}">
            <a href="{!! route('documents.index') !!}"><i class="fa fa-file"></i><span>Manajemen Arsip</span></a>
        </li>

{{-- Gerbang 2: Menu yang HANYA bisa dilihat ADMIN (Penguasa Tertinggi) --}}
@if(auth()->user()->is_super_admin)
    <li class="treeview {{ Request::is('admin/advanced*') ? 'active' : '' }}">
        <a href="#">
            <i class="fa fa-cogs"></i>
            <span>Pengaturan</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/advanced/settings*') ? 'active' : '' }}">
                <a href="{!! route('settings.index') !!}"><i class="fa fa-circle-o"></i><span>Pengaturan Umum</span></a>
            </li>
            <li class="{{ Request::is('admin/advanced/custom-fields*') ? 'active' : '' }}">
                <a href="{!! route('customFields.index') !!}"><i class="fa fa-circle-o"></i><span>Field Kustom</span></a>
            </li>
            <li class="{{ Request::is('admin/advanced/file-types*') ? 'active' : '' }}">
                <a href="{!! route('fileTypes.index') !!}"><i class="fa fa-circle-o"></i><span>Tipe File</span></a>
            </li>
        </ul>
    </li>
@endif

@if(!auth()->user()->is_super_admin)
<li class="{{ Request::is('admin/change-password*') ? 'active' : '' }}">
    <a href="{{ route('password.change') }}">
        <i class="fa fa-lock"></i> <span>Ganti Password</span>
    </a>
</li>
@endif