<?php

namespace App\DataTables;

use App\Models\Season;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class SeasonDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
{
    return (new EloquentDataTable($query))
        ->setRowId('id')
        ->filter(function ($query) {
            // Apply search filters dynamically
            if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                $search = strtolower(request()->input('search.value'));
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(season.season) LIKE ?', ["%$search%"]);

                });
            }
        })
        ->editColumn('billdate', function ($row) {
            // Format the billdate column to only show the date
            return \Carbon\Carbon::parse($row->billdate)->format('Y-m-d');
        })
        ->editColumn('action', function ($row) {
            return view('admin.inc.action', [
                'edit'   => 'admin.Season.edit',
                'delete' => 'admin.Season.destroy',
                'data'   => $row
            ]);
        })
        ->addColumn('sr_no', function ($row) {
            // Get the start index for pagination from the request
            $start = request()->get('start', 0); // Default to 0 if 'start' is not available
            static $serial = 0;

            // Increment the serial number based on the start index
            return $start + ++$serial;
        })
        ->rawColumns(['action']);
}


    /**
     * Get the query source of dataTable.
     */
    public function query(Season $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'season.*',
            ]);
    }


    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('Season-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'season', 'title' => __('Season '), 'orderable' => true, 'searchable' => true],
                ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false]
            ])
            ->minifiedAjax()
            ->orderBy(1)
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
        return 'Season_' . date('YmdHis');
    }
}
