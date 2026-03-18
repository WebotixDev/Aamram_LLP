<?php

namespace App\DataTables;

use App\Models\Transporter;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class TransporterDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->setRowId('id')

            // Global Search Filter
      ->filter(function ($query) {

    if (request()->has('search.value') && !empty(request()->input('search.value'))) {

        $search = strtolower(request()->input('search.value'));

        $query->where(function ($query) use ($search) {

            // Search supplier fields
            $query->whereRaw('LOWER(transporter) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(mobile_no) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"]);

        });
    }
})

            // Serial Number Column
            ->addColumn('sr_no', function ($row) {
                $start = request()->get('start', 0);
                static $serial = 0;
                return $start + ++$serial;
            })

            // Action Buttons
            ->addColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.Transporter.edit',
                    'delete' => 'admin.Transporter.destroy',
                    'data'   => $row
                ]);
            })

            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Transporter $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('transporter.*')
            ->orderBy('id', 'desc'); // ID wise DESC
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('Transporter-table')
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.'), 'orderable' => false, 'searchable' => false],
                ['data' => 'transporter', 'title' => __('Company Name')],
                ['data' => 'mobile_no', 'title' => __('Mobile No')],
                ['data' => 'address', 'title' => __('Address')],
                ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
            ])
            ->minifiedAjax()
            ->parameters([
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No Records Found',
                ],
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                    feather.replace();
                }',
            ]);
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Transporter_' . date('YmdHis');
    }
}
