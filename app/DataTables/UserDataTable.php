<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class UserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'users.datatables_actions')
            
            ->editColumn('status_akun', function ($user) {
                if ($user->status_akun == 'Aktif') {
                    return '<span class="label label-success" style="font-size: 12px;">Aktif</span>';
                } else {
                    return '<span class="label label-danger" style="font-size: 12px;">Diblokir</span>';
                }
            })
            
            ->rawColumns(['action', 'status_akun']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->with('data_divisi')->where('id', '!=', 1);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'nama_lengkap' => ['title' => 'Nama Lengkap'],
            'nip' => ['title' => 'NIP'],
            'jabatan' => ['title' => 'Jabatan'],
            
            // Kacamata Super: Panggil relasi data_divisi biar yang muncul namanya, bukan angkanya
            'divisi' => [
                'title' => 'Divisi', 
                'data' => 'data_divisi.nama_divisi', 
                'name' => 'data_divisi.nama_divisi',
                'defaultContent' => '-'
            ],
            
            'no_hp' => ['title' => 'No. HP / WA'],
            'username' => ['title' => 'Username Login'],
            'status_akun' => ['title' => 'Status Akun']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'usersdatatable_' . time();
    }
}
