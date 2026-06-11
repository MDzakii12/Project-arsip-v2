<?php

namespace App\DataTables;

use App\Tag;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class TagDataTable extends DataTable
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
        
        $dataTable = $dataTable->addColumn('action', 'tags.datatables_actions')
            ->editColumn('label_warna', function (Tag $tag) {
                return '<span class="label" style="background-color: ' . $tag->label_warna . '; padding: 5px 10px; border-radius: 4px; color: white;">' . $tag->label_warna . '</span>';
            })
            ->rawColumns(['label_warna', 'action']);

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Tag $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Tag $model)
    {
        return $model->newQuery();
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
            ->addAction(['width' => '120px', 'printable' => false, 'title' => 'Aksi'])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'desc']], 
                'buttons' => [
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
            'id_kategori' => ['title' => 'ID'],
            'nama_kategori' => ['title' => 'Nama Kategori'],
            'label_warna' => ['title' => 'Warna Kategori']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'KategoriArsip_' . time();
    }
}