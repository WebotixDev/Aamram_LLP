<?php

namespace App\DataTables;

use App\Models\Product_size;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class Product_sizeDataTable extends DataTable
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
                    $query->whereRaw('LOWER(product_size.status) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(product_size.product_size) LIKE ?', ["%$search%"]);
                });
            }
        })
        ->editColumn('billdate', function ($row) {
            // Format the billdate column to only show the date
            return \Carbon\Carbon::parse($row->billdate)->format('Y-m-d');
        })
        ->editColumn('action', function ($row) {
            return view('admin.inc.action', [
                'edit'   => 'admin.product_size.edit',
                'delete' => 'admin.product_size.destroy',
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
    public function query(Product_size $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'product_size.*',
            ]);
           
        }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('Product_size-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'product_size', 'title' => __('Product size'), 'orderable' => true, 'searchable' => true],
                ['data' => 'type', 'title' => __('Type'), 'orderable' => true, 'searchable' => true],
                ['data' => 'status', 'title' => __('Status'), 'orderable' => true, 'searchable' => true],
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
        return 'Product_size' . date('YmdHis');
    }
}
